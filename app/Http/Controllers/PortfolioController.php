<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Trade;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->is_admin) {
            return redirect()->route('assets.index');
        }

        $assets = Asset::all();
        $portfolio = [];
        $totalPortfolioValue = 0;

        foreach ($assets as $asset) {
            $totalBought = Trade::where('user_id', $user->id)
                ->where('asset_id', $asset->id)
                ->where('type', 'buy')
                ->sum('amount');

            $totalSold = Trade::where('user_id', $user->id)
                ->where('asset_id', $asset->id)
                ->where('type', 'sell')
                ->sum('amount');

            $holdings = max(0, (float) $totalBought - (float) $totalSold);

            if ($holdings > 0) {
                $currentValue = $asset->price ? $holdings * (float) $asset->price : null;
                
                $portfolio[] = [
                    'asset' => $asset,
                    'holdings' => $holdings,
                    'current_price' => $asset->price,
                    'current_value' => $currentValue,
                ];

                if ($currentValue !== null) {
                    $totalPortfolioValue += $currentValue;
                }
            }
        }

        return view('portfolio.index', compact('portfolio', 'totalPortfolioValue'));
    }
}
