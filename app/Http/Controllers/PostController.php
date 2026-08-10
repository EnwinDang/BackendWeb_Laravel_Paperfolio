<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'likers'])
            ->withCount('likers')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('feed.index', compact('posts'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->is_admin) {
            abort(403, 'Admins cannot post to the feed.');
        }

        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Posted to the feed.');
    }

    public function like(Post $post)
    {
        $user = Auth::user();

        if (!$post->likers()->where('users.id', $user->id)->exists()) {
            $post->likers()->attach($user->id);
        }

        return back();
    }

    public function unlike(Post $post)
    {
        $post->likers()->detach(Auth::id());

        return back();
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        $post->delete();

        return back()->with('success', 'Post deleted.');
    }
}
