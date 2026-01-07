<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        // Get the week to display (default to current week)
        $weekStart = $request->get('week', null);
        
        if ($weekStart) {
            $weekStart = Carbon::parse($weekStart)->startOfWeek();
        } else {
            $weekStart = Carbon::now()->startOfWeek();
        }
        
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        // Get all non-admin users
        $users = User::where('is_admin', false)->get();
        
        $leaderboard = [];
        
        foreach ($users as $user) {
            // Get all sell trades for this user in the specified week
            $sellTrades = Trade::where('user_id', $user->id)
                ->where('type', 'sell')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->orderBy('created_at')
                ->get();
            
            if ($sellTrades->isEmpty()) {
                continue; // Skip users with no trades this week
            }
            
            $totalRealizedProfit = 0;
            $totalCostBasis = 0;
            
            // Calculate realized profit for each sell trade
            foreach ($sellTrades as $sellTrade) {
                // Get all buy trades for this asset before this sell trade
                $buyTrades = Trade::where('user_id', $user->id)
                    ->where('asset_id', $sellTrade->asset_id)
                    ->where('type', 'buy')
                    ->where('created_at', '<=', $sellTrade->created_at)
                    ->orderBy('created_at')
                    ->get();
                
                // Calculate average purchase price at time of sale
                $totalBoughtAmount = $buyTrades->sum('amount');
                $totalCost = 0;
                foreach ($buyTrades as $buyTrade) {
                    $totalCost += $buyTrade->amount * $buyTrade->price_snapshot;
                }
                
                $avgPurchasePrice = $totalBoughtAmount > 0 ? $totalCost / $totalBoughtAmount : 0;
                
                // Calculate profit for this sell trade
                $sellAmount = (float) $sellTrade->amount;
                $sellPrice = (float) $sellTrade->price_snapshot;
                $costBasis = $avgPurchasePrice * $sellAmount;
                $profit = ($sellPrice - $avgPurchasePrice) * $sellAmount;
                
                $totalRealizedProfit += $profit;
                $totalCostBasis += $costBasis;
            }
            
            // Calculate realized profit percentage
            $realizedProfitPercent = $totalCostBasis > 0 
                ? ($totalRealizedProfit / $totalCostBasis) * 100 
                : 0;
            
            $leaderboard[] = [
                'user' => $user,
                'realized_profit' => $totalRealizedProfit,
                'cost_basis' => $totalCostBasis,
                'realized_profit_percent' => $realizedProfitPercent,
                'num_trades' => $sellTrades->count(),
            ];
        }
        
        // Sort by realized profit percentage (descending)
        usort($leaderboard, function($a, $b) {
            return $b['realized_profit_percent'] <=> $a['realized_profit_percent'];
        });
        
        // Get previous and next week for navigation
        $previousWeek = $weekStart->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $weekStart->copy()->addWeek()->format('Y-m-d');
        $currentWeek = $weekStart->format('Y-m-d');
        
        return view('leaderboard.index', compact(
            'leaderboard',
            'weekStart',
            'weekEnd',
            'previousWeek',
            'nextWeek',
            'currentWeek'
        ));
    }
}
