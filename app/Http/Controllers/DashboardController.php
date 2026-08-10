<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->is_admin) {
            return redirect()->route('assets.index');
        }

        $trades = Trade::where('user_id', $user->id)
            ->with('asset')
            ->orderBy('created_at', 'desc')
            ->get();

        $assets = Asset::all();
        $portfolio = [];
        $assetsWithOwned = [];
        $watchedAssetIds = $user->watchedAssets()->pluck('assets.id')->toArray();

        foreach ($assets as $asset) {
            $ownedAmount = $this->getOwnedAmount($user->id, $asset->id);
            $assetsWithOwned[] = [
                'asset' => $asset,
                'owned' => $ownedAmount,
                'is_watched' => in_array($asset->id, $watchedAssetIds),
            ];
            
            if ($ownedAmount > 0) {
                $portfolio[] = [
                    'asset' => $asset,
                    'owned' => $ownedAmount,
                ];
            }
        }

        $cashBalance = $user->getCashBalance();
        $watchedAssets = $user->watchedAssets()->get();
        $openPositions = $user->positions()->where('status', 'open')->with('asset')->get();

        return view('dashboard', compact('trades', 'portfolio', 'assetsWithOwned', 'cashBalance', 'watchedAssets', 'openPositions'));
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
}
