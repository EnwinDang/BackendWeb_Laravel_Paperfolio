<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect(auth()->user()->is_admin ? route('assets.index') : route('dashboard'));
        }

        $trendingPosts = Post::with('user')
            ->withCount('likers')
            ->orderByDesc('likers_count')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $traderCount = User::where('is_admin', false)->count();

        return view('welcome', compact('trendingPosts', 'traderCount'));
    }
}
