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
        // Fetch trades for the user, ordered by most recent first
        $trades = Trade::where('user_id', $user->id)
            ->with('asset')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('profile.show', compact('user', 'trades'));
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
