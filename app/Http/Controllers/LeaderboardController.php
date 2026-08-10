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
        $view = $request->get('view', 'weekly');
        if (!in_array($view, ['weekly', 'monthly', 'yearly'], true)) {
            $view = 'weekly';
        }

        if ($view === 'yearly') {
            $year = (int) $request->get('year', Carbon::now()->year);
            $periodStart = Carbon::create($year, 1, 1)->startOfYear();
            $periodEnd = $periodStart->copy()->endOfYear();
            $previousPeriod = (string) ($year - 1);
            $nextPeriod = (string) ($year + 1);
            $currentPeriod = (string) $year;
        } elseif ($view === 'monthly') {
            $month = $request->get('month', null);
            $periodStart = $month
                ? Carbon::parse($month . '-01')->startOfMonth()
                : Carbon::now()->startOfMonth();
            $periodEnd = $periodStart->copy()->endOfMonth();
            $previousPeriod = $periodStart->copy()->subMonth()->format('Y-m');
            $nextPeriod = $periodStart->copy()->addMonth()->format('Y-m');
            $currentPeriod = $periodStart->format('Y-m');
        } else {
            $week = $request->get('week', null);
            $periodStart = $week
                ? Carbon::parse($week)->startOfWeek()
                : Carbon::now()->startOfWeek();
            $periodEnd = $periodStart->copy()->endOfWeek();
            $previousPeriod = $periodStart->copy()->subWeek()->format('Y-m-d');
            $nextPeriod = $periodStart->copy()->addWeek()->format('Y-m-d');
            $currentPeriod = $periodStart->format('Y-m-d');
        }

        $leaderboard = $this->buildLeaderboard([$periodStart, $periodEnd]);

        return view('leaderboard.index', compact(
            'leaderboard',
            'periodStart',
            'periodEnd',
            'previousPeriod',
            'nextPeriod',
            'currentPeriod',
            'view'
        ));
    }

    /**
     * Build the ranked leaderboard from realized profit on sell trades within $dateRange = [start, end].
     */
    private function buildLeaderboard(array $dateRange): array
    {
        $users = User::where('is_admin', false)->get();

        $leaderboard = [];

        foreach ($users as $user) {
            $sellQuery = Trade::where('user_id', $user->id)->where('type', 'sell');

            if ($dateRange) {
                $sellQuery->whereBetween('created_at', $dateRange);
            }

            $sellTrades = $sellQuery->orderBy('created_at')->get();

            if ($sellTrades->isEmpty()) {
                continue;
            }

            $totalRealizedProfit = 0;
            $totalCostBasis = 0;
            $profitableTrades = 0;

            foreach ($sellTrades as $sellTrade) {
                // Average purchase price at the time of this sale (all buys up to that point)
                $buyTrades = Trade::where('user_id', $user->id)
                    ->where('asset_id', $sellTrade->asset_id)
                    ->where('type', 'buy')
                    ->where('created_at', '<=', $sellTrade->created_at)
                    ->orderBy('created_at')
                    ->get();

                $totalBoughtAmount = $buyTrades->sum('amount');
                $totalCost = 0;
                foreach ($buyTrades as $buyTrade) {
                    $totalCost += $buyTrade->amount * $buyTrade->price_snapshot;
                }

                $avgPurchasePrice = $totalBoughtAmount > 0 ? $totalCost / $totalBoughtAmount : 0;

                $sellAmount = (float) $sellTrade->amount;
                $sellPrice = (float) $sellTrade->price_snapshot;
                $costBasis = $avgPurchasePrice * $sellAmount;
                $profit = ($sellPrice - $avgPurchasePrice) * $sellAmount;

                $totalRealizedProfit += $profit;
                $totalCostBasis += $costBasis;
                if ($profit > 0) {
                    $profitableTrades++;
                }
            }

            $realizedProfitPercent = $totalCostBasis > 0
                ? ($totalRealizedProfit / $totalCostBasis) * 100
                : 0;

            $winRate = $sellTrades->count() > 0
                ? ($profitableTrades / $sellTrades->count()) * 100
                : 0;

            // Volume = total $ traded (buy + sell) over the same period
            $volumeQuery = Trade::where('user_id', $user->id);
            if ($dateRange) {
                $volumeQuery->whereBetween('created_at', $dateRange);
            }
            $volume = $volumeQuery->get()->sum(fn ($trade) => (float) $trade->amount * (float) $trade->price_snapshot);

            $leaderboard[] = [
                'user' => $user,
                'realized_profit' => $totalRealizedProfit,
                'cost_basis' => $totalCostBasis,
                'realized_profit_percent' => $realizedProfitPercent,
                'num_trades' => $sellTrades->count(),
                'win_rate' => $winRate,
                'volume' => $volume,
            ];
        }

        usort($leaderboard, function ($a, $b) {
            return $b['realized_profit_percent'] <=> $a['realized_profit_percent'];
        });

        return $leaderboard;
    }
}
