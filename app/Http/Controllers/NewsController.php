<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderBy('publication_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('news.index', compact('news'));
    }

    public function show(News $news)
    {
        $news->load(['comments.user']);
        return view('news.show', compact('news'));
    }

    public function create()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Only admins can create news.');
        }
        return view('news.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Only admins can create news.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'content' => 'required|string',
            'publication_date' => 'nullable|date',
        ], [
            'title.required' => 'The title field is required.',
            'content.required' => 'The content field is required.',
        ]);

        $publicationDate = null;
        if ($request->publication_date) {
            try {
                $publicationDate = \Carbon\Carbon::parse($request->publication_date);
            } catch (\Exception $e) {
                $publicationDate = now();
            }
        } else {
            $publicationDate = now();
        }

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'publication_date' => $publicationDate,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news', 'public');
            $data['image'] = $path;
        }

        News::create($data);

        return redirect()->route('news.index')->with('success', 'News item created successfully.');
    }

    public function edit(News $news)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Only admins can edit news.');
        }
        return view('news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Only admins can update news.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'content' => 'required|string',
            'publication_date' => 'nullable|date',
        ]);

        $publicationDate = null;
        if ($request->publication_date) {
            try {
                $publicationDate = \Carbon\Carbon::parse($request->publication_date);
            } catch (\Exception $e) {
                $publicationDate = $news->publication_date ?? now();
            }
        } else {
            $publicationDate = $news->publication_date ?? now();
        }

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'publication_date' => $publicationDate,
        ];

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $path = $request->file('image')->store('news', 'public');
            $data['image'] = $path;
        }

        $news->update($data);

        return redirect()->route('news.index')->with('success', 'News item updated successfully.');
    }

    public function destroy(News $news)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'Only admins can delete news.');
        }

        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }
        $news->delete();
        return redirect()->route('news.index')->with('success', 'News item deleted successfully.');
    }

    public function storeComment(Request $request, News $news)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        NewsComment::create([
            'news_id' => $news->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->route('news.show', $news)->with('success', 'Comment added successfully.');
    }

    public function destroyComment(NewsComment $comment)
    {
        // Only allow admins or the comment owner to delete
        if (!Auth::check() || (!Auth::user()->is_admin && Auth::id() !== $comment->user_id)) {
            abort(403, 'Unauthorized action.');
        }

        $news = $comment->news;
        $comment->delete();

        return redirect()->route('news.show', $news)->with('success', 'Comment deleted successfully.');
    }
}
