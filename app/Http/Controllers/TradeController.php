<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TradeController extends Controller
{
    public function buy(Request $request, Asset $asset)
    {
        if (Auth::user()->is_admin) {
            abort(403, 'Admins cannot make trades.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.00000001',
        ]);

        if (!$asset->price) {
            return back()->withErrors(['error' => 'Asset price is not available.']);
        }

        Trade::create([
            'user_id' => Auth::id(),
            'asset_id' => $asset->id,
            'type' => 'buy',
            'amount' => $request->amount,
            'price_snapshot' => $asset->price,
        ]);

        return redirect()->route('dashboard')->with('success', 'Buy order executed successfully.');
    }

    public function sell(Request $request, Asset $asset)
    {
        if (Auth::user()->is_admin) {
            abort(403, 'Admins cannot make trades.');
        }

        $ownedAmount = $this->getOwnedAmount(Auth::id(), $asset->id);

        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.00000001',
                'max:' . $ownedAmount,
            ],
        ], [
            'amount.max' => 'You cannot sell more than you own. You own ' . number_format($ownedAmount, 8) . ' ' . $asset->symbol . '.',
        ]);

        if (!$asset->price) {
            return back()->withErrors(['error' => 'Asset price is not available.']);
        }

        Trade::create([
            'user_id' => Auth::id(),
            'asset_id' => $asset->id,
            'type' => 'sell',
            'amount' => $request->amount,
            'price_snapshot' => $asset->price,
        ]);

        return redirect()->route('dashboard')->with('success', 'Sell order executed successfully.');
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
