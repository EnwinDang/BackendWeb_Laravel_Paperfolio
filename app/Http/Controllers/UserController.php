<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'nullable|boolean',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin') ? true : false,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function toggleAdmin(User $user)
    {
        $user->update([
            'is_admin' => !$user->is_admin,
        ]);

        $status = $user->is_admin ? 'promoted to admin' : 'removed from admin';
        return redirect()->route('users.index')->with('success', "User {$status} successfully.");
    }

    public function toggleSuspend(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot suspend your own account.');
        }

        $user->update(['is_suspended' => !$user->is_suspended]);

        $status = $user->is_suspended ? 'suspended' : 'unsuspended';
        return redirect()->route('users.index')->with('success', "User {$status} successfully.");
    }

    // GDPR-style "right to erasure": scrubs personal data instead of hard-deleting
    // the row, since trades/posts/messages/comments reference this user and a real
    // delete would either break referential integrity or cascade into other users'
    // data (e.g. their side of a DM thread, likes on their posts).
    public function erase(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot erase your own account.');
        }

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->update([
            'name' => 'Deleted User',
            'email' => 'deleted-' . $user->id . '-' . Str::random(8) . '@erased.local',
            'password' => Hash::make(Str::random(32)),
            'username' => null,
            'date_of_birth' => null,
            'profile_picture' => null,
            'about_me' => null,
            'is_admin' => false,
            'is_suspended' => true,
            'is_anonymized' => true,
            'show_portfolio' => false,
            'show_age' => false,
            'show_email' => false,
        ]);

        return redirect()->route('users.index')->with('success', 'User data erased successfully.');
    }
}
