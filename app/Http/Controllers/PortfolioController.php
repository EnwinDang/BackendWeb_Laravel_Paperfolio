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
        $totalInvestedCurrent = 0; // Cost basis of current holdings
        $totalInvestedAll = 0; // Total amount invested in all buy trades
        $totalUnrealizedProfit = 0;
        $totalRealizedProfit = 0;

        foreach ($assets as $asset) {
            $buyTrades = Trade::where('user_id', $user->id)
                ->where('asset_id', $asset->id)
                ->where('type', 'buy')
                ->get();

            $sellTrades = Trade::where('user_id', $user->id)
                ->where('asset_id', $asset->id)
                ->where('type', 'sell')
                ->get();

            $totalBought = $buyTrades->sum('amount');
            $totalSold = $sellTrades->sum('amount');
            $holdings = max(0, (float) $totalBought - (float) $totalSold);

            // Calculate total invested in all buy trades
            $totalInvestedInAsset = 0;
            $totalBoughtAmount = 0;
            foreach ($buyTrades as $trade) {
                $totalInvestedInAsset += $trade->amount * $trade->price_snapshot;
                $totalBoughtAmount += $trade->amount;
            }

            // Calculate average purchase price
            $averagePurchasePrice = $totalBoughtAmount > 0 ? $totalInvestedInAsset / $totalBoughtAmount : 0;

            // Calculate cost basis for current holdings (proportional to what's left)
            $costBasis = $holdings > 0 && $totalBoughtAmount > 0 
                ? ($totalInvestedInAsset / $totalBoughtAmount) * $holdings 
                : 0;

            // Calculate realized profit from sold trades
            $realizedProfit = 0;
            foreach ($sellTrades as $sellTrade) {
                // Find average purchase price at time of sale
                $boughtBeforeSale = Trade::where('user_id', $user->id)
                    ->where('asset_id', $asset->id)
                    ->where('type', 'buy')
                    ->where('created_at', '<=', $sellTrade->created_at)
                    ->get();
                
                $totalBoughtBeforeSale = $boughtBeforeSale->sum('amount');
                $totalCostBeforeSale = 0;
                foreach ($boughtBeforeSale as $buyTrade) {
                    $totalCostBeforeSale += $buyTrade->amount * $buyTrade->price_snapshot;
                }
                
                $avgPriceAtSale = $totalBoughtBeforeSale > 0 ? $totalCostBeforeSale / $totalBoughtBeforeSale : 0;
                $realizedProfit += ($sellTrade->price_snapshot - $avgPriceAtSale) * $sellTrade->amount;
            }

            // Calculate unrealized profit for current holdings
            $currentValue = $asset->price ? $holdings * (float) $asset->price : null;
            $unrealizedProfit = $currentValue !== null ? $currentValue - $costBasis : 0;

            if ($holdings > 0 || count($sellTrades) > 0) {
                $portfolio[] = [
                    'asset' => $asset,
                    'holdings' => $holdings,
                    'current_price' => $asset->price,
                    'current_value' => $currentValue,
                    'cost_basis' => $costBasis,
                    'average_purchase_price' => $averagePurchasePrice,
                    'unrealized_profit' => $unrealizedProfit,
                    'unrealized_profit_percent' => $costBasis > 0 ? ($unrealizedProfit / $costBasis) * 100 : 0,
                    'realized_profit' => $realizedProfit,
                ];

                if ($currentValue !== null) {
                    $totalPortfolioValue += $currentValue;
                }
                $totalInvestedCurrent += $costBasis;
                $totalInvestedAll += $totalInvestedInAsset; // Total invested in all buy trades
                $totalUnrealizedProfit += $unrealizedProfit;
                $totalRealizedProfit += $realizedProfit;
            }
        }

        $totalProfit = $totalUnrealizedProfit + $totalRealizedProfit;
        // Calculate percentage based on total invested (all buy trades), not just current holdings
        $totalInvested = $totalInvestedAll > 0 ? $totalInvestedAll : $totalInvestedCurrent;
        $totalProfitPercent = $totalInvested > 0 ? ($totalProfit / $totalInvested) * 100 : 0;

        $cashBalance = $user->getCashBalance();

        return view('portfolio.index', compact(
            'portfolio', 
            'totalPortfolioValue', 
            'totalInvested',
            'totalInvestedCurrent',
            'totalUnrealizedProfit',
            'totalRealizedProfit',
            'totalProfit',
            'totalProfitPercent',
            'cashBalance'
        ));
    }
}
