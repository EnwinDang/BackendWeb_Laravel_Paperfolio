<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Post;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::all();
        $assetsWithOwned = [];

        $cashBalance = null;
        $watchedAssetIds = [];

        if (Auth::check() && !Auth::user()->is_admin) {
            $user = Auth::user();
            $cashBalance = $user->getCashBalance();
            $watchedAssetIds = $user->watchedAssets()->pluck('assets.id')->toArray();

            foreach ($assets as $asset) {
                $ownedAmount = $this->getOwnedAmount(Auth::id(), $asset->id);
                $assetsWithOwned[] = [
                    'asset' => $asset,
                    'owned' => $ownedAmount,
                    'is_watched' => in_array($asset->id, $watchedAssetIds),
                ];
            }
        } else {
            foreach ($assets as $asset) {
                $assetsWithOwned[] = [
                    'asset' => $asset,
                    'owned' => 0,
                    'is_watched' => false,
                ];
            }
        }

        return view('assets.index', compact('assetsWithOwned', 'cashBalance'));
    }

    /**
     * The per-asset trading terminal: chart, spot buy/sell, leverage panel,
     * open positions on this asset, and posts that $cashtag this symbol.
     */
    public function show(Asset $asset)
    {
        $owned = 0;
        $cashBalance = null;
        $isWatched = false;
        $openPositions = collect();

        if (Auth::check() && !Auth::user()->is_admin) {
            $user = Auth::user();
            $owned = $this->getOwnedAmount($user->id, $asset->id);
            $cashBalance = $user->getCashBalance();
            $isWatched = $user->watchedAssets()->where('asset_id', $asset->id)->exists();
            $openPositions = $user->positions()
                ->where('asset_id', $asset->id)
                ->where('status', 'open')
                ->get();
        }

        $posts = Post::with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(fn ($post) => in_array($asset->symbol, $post->cashtags()))
            ->values();

        return view('assets.show', compact('asset', 'owned', 'cashBalance', 'isWatched', 'openPositions', 'posts'));
    }

    private function getOwnedAmount(int $userId, int $assetId): float
    {
        $buyAmount = Trade::where('user_id', $userId)
            ->where('asset_id', $assetId)
            ->where('type', 'buy')
            ->sum('amount');

        $sellAmount = Trade::where('user_id', $userId)
            ->where('asset_id', $assetId)
            ->where('type', 'sell')
            ->sum('amount');

        return max(0, (float) $buyAmount - (float) $sellAmount);
    }

    public function create()
    {
        $coins = $this->getCoinGeckoCoins();
        return view('assets.create', compact('coins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'symbol' => 'required|string|unique:assets,symbol',
            'coingecko_id' => 'nullable|string',
        ]);

        $asset = Asset::create([
            'name' => $request->name,
            'symbol' => strtoupper($request->symbol),
            'coingecko_id' => $request->coingecko_id,
        ]);

        // Fetch price immediately if coingecko_id is provided
        if ($asset->coingecko_id) {
            $this->updateAssetPrice($asset);
        }

        return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
    }

    public function edit(Asset $asset)
    {
        $coins = $this->getCoinGeckoCoins();
        return view('assets.edit', compact('asset', 'coins'));
    }

    private function getCoinGeckoCoins()
    {
        return Cache::remember('coingecko_coins_list', 3600, function () {
            try {
                $response = Http::timeout(10)->get('https://api.coingecko.com/api/v3/coins/list');
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                return [];
            } catch (\Exception $e) {
                \Log::error('Failed to fetch CoinGecko coins list: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Update the price for a single asset from CoinGecko
     */
    private function updateAssetPrice(Asset $asset)
    {
        if (empty($asset->coingecko_id)) {
            return false;
        }

        try {
            $response = Http::timeout(10)->get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => $asset->coingecko_id,
                'vs_currencies' => 'usd',
                'include_24hr_change' => 'true',
            ]);

            if ($response->successful()) {
                $prices = $response->json();

                if (isset($prices[$asset->coingecko_id]['usd'])) {
                    $price = (float) $prices[$asset->coingecko_id]['usd'];
                    $change24h = isset($prices[$asset->coingecko_id]['usd_24h_change'])
                        ? (float) $prices[$asset->coingecko_id]['usd_24h_change']
                        : null;

                    $asset->update([
                        'price' => $price,
                        'price_change_24h' => $change24h,
                        'price_last_updated_at' => now(),
                    ]);

                    return true;
                }
            }
            
            Log::warning("Failed to update price for asset {$asset->symbol} (CoinGecko ID: {$asset->coingecko_id})");
            return false;
        } catch (\Exception $e) {
            Log::error("Error updating price for asset {$asset->symbol}: " . $e->getMessage());
            return false;
        }
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'name' => 'required|string',
            'symbol' => 'required|string|unique:assets,symbol,' . $asset->id,
            'coingecko_id' => 'nullable|string',
        ]);

        $hadCoingeckoId = !empty($asset->coingecko_id);
        $coingeckoIdChanged = $asset->coingecko_id !== $request->coingecko_id;

        $asset->update([
            'name' => $request->name,
            'symbol' => strtoupper($request->symbol),
            'coingecko_id' => $request->coingecko_id,
        ]);

        // Fetch price if coingecko_id was added or changed
        if ($asset->coingecko_id && ($coingeckoIdChanged || !$hadCoingeckoId)) {
            $this->updateAssetPrice($asset);
        }

        return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }

    /**
     * Add asset to user's watchlist
     */
    public function addToWatchlist(Asset $asset)
    {
        if (!Auth::check() || Auth::user()->is_admin) {
            abort(403, 'Only regular users can add assets to watchlist.');
        }

        $user = Auth::user();
        
        if (!$user->watchedAssets()->where('asset_id', $asset->id)->exists()) {
            $user->watchedAssets()->attach($asset->id);
            return back()->with('success', $asset->symbol . ' added to watchlist.');
        }

        return back()->with('info', $asset->symbol . ' is already in your watchlist.');
    }

    /**
     * Remove asset from user's watchlist
     */
    public function removeFromWatchlist(Asset $asset)
    {
        if (!Auth::check() || Auth::user()->is_admin) {
            abort(403, 'Only regular users can remove assets from watchlist.');
        }

        $user = Auth::user();
        $user->watchedAssets()->detach($asset->id);

        return back()->with('success', $asset->symbol . ' removed from watchlist.');
    }
}
