@extends('layouts.app')

@section('title', $news->title)

@section('content')
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('news.index') }}" class="btn btn-secondary">← Back to News</a>
        @auth
            @if(auth()->user()->is_admin)
                <a href="{{ route('news.edit', $news) }}" class="btn btn-primary" style="margin-left: 0.5rem;">Edit</a>
                <form method="POST" action="{{ route('news.destroy', $news) }}" style="display: inline; margin-left: 0.5rem;" onsubmit="return confirm('Are you sure you want to delete this news item?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            @endif
        @endauth
    </div>

    <div class="card">
        <h1>{{ $news->title }}</h1>
        <div style="color: var(--gray); font-size: 0.875rem; margin-bottom: 1rem;">{{ $news->publication_date->format('F j, Y') }}</div>

        @if($news->image)
            <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}" style="max-width: 100%; height: auto; margin-bottom: 1rem; border-radius: 8px;">
        @endif

        <div style="line-height: 1.8;">
            {!! nl2br(e($news->content)) !!}
        </div>
    </div>

    <div class="card" style="margin-top: 2rem;">
        <h2>Comments ({{ $news->comments->count() }})</h2>

        @auth
            <div style="margin-bottom: 2rem;">
                <form method="POST" action="{{ route('news.comments.store', $news) }}">
                    @csrf
                    <div class="form-group">
                        <label for="content">Add a comment</label>
                        <textarea 
                            id="content" 
                            name="content" 
                            required 
                            rows="4"
                            placeholder="Write your comment here..."
                            style="min-height: 100px;"
                        >{{ old('content') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Post Comment</button>
                </form>
            </div>
        @else
            <div style="padding: 1rem; background: var(--gray-light); border-radius: 6px; margin-bottom: 2rem;">
                <p style="margin: 0; color: var(--gray);">
                    <a href="{{ route('login') }}" style="color: var(--dark-blue);">Login</a> or 
                    <a href="{{ route('register') }}" style="color: var(--dark-blue);">Register</a> to leave a comment.
                </p>
            </div>
        @endauth

        @if($news->comments->isEmpty())
            <div class="empty">
                <p>No comments yet. Be the first to comment!</p>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                @foreach($news->comments as $comment)
                    <div style="padding: 1rem; background: var(--gray-light); border-radius: 6px; border-left: 3px solid var(--dark-blue);">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                            <div>
                                <strong>
                                    <a href="{{ route('profile.show', $comment->user) }}" style="color: var(--dark-blue); text-decoration: none;">
                                        {{ $comment->user->username ?? $comment->user->name }}
                                    </a>
                                </strong>
                                <span style="color: var(--gray); font-size: 0.875rem; margin-left: 0.5rem;">
                                    {{ $comment->created_at->format('M j, Y \a\t g:i A') }}
                                </span>
                            </div>
                            @auth
                                @if(auth()->user()->is_admin || auth()->id() === $comment->user_id)
                                    <form method="POST" action="{{ route('news.comments.destroy', $comment) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                        <div style="line-height: 1.6; color: var(--gray-dark);">
                            {!! nl2br(e($comment->content)) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
