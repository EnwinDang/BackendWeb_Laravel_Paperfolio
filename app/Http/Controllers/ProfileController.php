<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $canViewPortfolio = $user->portfolioVisibleTo(Auth::user());

        // Fetch trades for the user, ordered by most recent first
        $trades = $canViewPortfolio
            ? Trade::where('user_id', $user->id)->with('asset')->orderBy('created_at', 'desc')->get()
            : collect();

        // "Trading since" is based on their first trade, not account creation
        $firstTrade = Trade::where('user_id', $user->id)->oldest('created_at')->first();
        $firstTradeAt = $firstTrade?->created_at;

        // Total portfolio value (in $) is always shown on every profile, regardless
        // of the show_portfolio privacy setting, which only gates the detailed trade history.
        $portfolioValue = $user->is_admin ? null : $user->getTotalPortfolioValue();

        return view('profile.show', compact('user', 'trades', 'canViewPortfolio', 'firstTradeAt', 'portfolioValue'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'profile_picture' => 'nullable|image|max:2048',
            'about_me' => 'nullable|string|max:1000',
        ]);

        $data = [
            'username' => $request->username,
            'date_of_birth' => $request->date_of_birth,
            'about_me' => $request->about_me,
            'show_portfolio' => $request->has('show_portfolio'),
            'show_age' => $request->has('show_age'),
            'show_email' => $request->has('show_email'),
        ];

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        $user->update($data);

        return redirect()->route('profile.show', $user)->with('success', 'Profile updated successfully.');
    }
}
