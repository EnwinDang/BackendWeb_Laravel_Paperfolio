<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    public function open(Request $request, Asset $asset)
    {
        if (Auth::user()->is_admin) {
            abort(403, 'Admins cannot make trades.');
        }

        if (!$asset->price) {
            return back()->withErrors(['error' => 'Asset price is not available.']);
        }

        $user = Auth::user();
        $availableCash = $user->getCashBalance();

        $request->validate([
            'direction' => 'required|in:long,short',
            'leverage' => 'required|integer|in:5,10,100',
            'margin_usd' => 'required|numeric|min:0.01|max:' . $availableCash,
        ], [
            'margin_usd.max' => 'Insufficient funds. You have $' . number_format($availableCash, 2) . ' available.',
        ]);

        Position::create([
            'user_id' => $user->id,
            'asset_id' => $asset->id,
            'direction' => $request->direction,
            'leverage' => $request->leverage,
            'margin_usd' => $request->margin_usd,
            'entry_price' => $asset->price,
            'status' => 'open',
        ]);

        return back()->with('success', ucfirst($request->direction) . ' position opened on ' . $asset->symbol . ' at ' . $request->leverage . 'x leverage.');
    }

    public function close(Position $position)
    {
        if ($position->user_id !== Auth::id()) {
            abort(403);
        }

        if ($position->status !== 'open') {
            return back()->withErrors(['error' => 'This position is already closed.']);
        }

        $asset = $position->asset;

        if (!$asset->price) {
            return back()->withErrors(['error' => 'Asset price is not available.']);
        }

        $markPrice = (float) $asset->price;

        $position->update([
            'close_price' => $markPrice,
            'realized_pnl' => $position->unrealizedPnl($markPrice),
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Position closed.');
    }
}
