<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Notifications\PostRemoved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'likers'])->withCount('likers');

        if ($request->filled('user')) {
            $search = $request->user;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('q')) {
            $query->where('content', 'like', '%' . $request->q . '%');
        }

        $posts = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

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

        if ($user->is_admin) {
            abort(403, 'Admins cannot like posts.');
        }

        if (!$post->likers()->where('users.id', $user->id)->exists()) {
            $post->likers()->attach($user->id);
        }

        return response()->json(['liked' => true, 'count' => $post->likers()->count()]);
    }

    public function unlike(Post $post)
    {
        if (Auth::user()->is_admin) {
            abort(403, 'Admins cannot like posts.');
        }

        $post->likers()->detach(Auth::id());

        return response()->json(['liked' => false, 'count' => $post->likers()->count()]);
    }

    public function destroy(Request $request, Post $post)
    {
        $isOwnPost = $post->user_id === Auth::id();

        if (!$isOwnPost && !Auth::user()->is_admin) {
            abort(403);
        }

        // Only an admin removing someone else's post needs to give a reason -
        // deleting your own post doesn't need to explain itself to anyone.
        if (!$isOwnPost) {
            $request->validate([
                'reason' => 'required|in:' . implode(',', array_keys(Post::DELETION_REASONS)),
                'custom_reason' => 'required_if:reason,other|nullable|string|max:500',
            ]);

            $post->user->notify(new PostRemoved(
                $post->content,
                Post::DELETION_REASONS[$request->reason],
                $request->reason === 'other' ? $request->custom_reason : null,
            ));
        }

        $post->delete();

        return back()->with('success', 'Post deleted.');
    }
}
