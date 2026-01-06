<?php

namespace App\Http\Controllers;

use App\Models\Asset;
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

        if (Auth::check() && !Auth::user()->is_admin) {
            foreach ($assets as $asset) {
                $ownedAmount = $this->getOwnedAmount(Auth::id(), $asset->id);
                $assetsWithOwned[] = [
                    'asset' => $asset,
                    'owned' => $ownedAmount,
                ];
            }
        } else {
            foreach ($assets as $asset) {
                $assetsWithOwned[] = [
                    'asset' => $asset,
                    'owned' => 0,
                ];
            }
        }

        return view('assets.index', compact('assetsWithOwned'));
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
            ]);

            if ($response->successful()) {
                $prices = $response->json();
                
                if (isset($prices[$asset->coingecko_id]['usd'])) {
                    $price = (float) $prices[$asset->coingecko_id]['usd'];
                    
                    $asset->update([
                        'price' => $price,
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

    public function updatePrice(Asset $asset)
    {
        if (!$asset->coingecko_id) {
            return back()->with('error', 'Asset does not have a CoinGecko ID.');
        }

        $success = $this->updateAssetPrice($asset);

        if ($success) {
            return back()->with('success', "Price updated successfully for {$asset->symbol}.");
        } else {
            return back()->with('error', "Failed to update price for {$asset->symbol}. Please check the CoinGecko ID.");
        }
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }
}
