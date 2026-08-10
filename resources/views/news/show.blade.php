@extends(auth()->check() ? (auth()->user()->is_admin ? 'layouts.app' : 'layouts.dashboard') : 'layouts.marketing')

@section('title', $news->title)

@push('styles')
<style>
    .comment-item {
        padding: 1rem 1.1rem;
        background: var(--page-bg);
        border: 2px solid var(--ink);
        border-left-width: 5px;
    }
    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .comment-author {
        color: var(--ink);
        text-decoration: none;
        font-weight: 800;
    }
    .comment-time {
        color: var(--text-gray);
        font-size: 0.8rem;
    }
</style>
@endpush

@section('content')
    <div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ route('news.index') }}" class="btn btn-secondary">← Back to Announcements</a>
        @auth
            @if(auth()->user()->is_admin)
                <a href="{{ route('news.edit', $news) }}" class="btn btn-primary">Edit</a>
                <form method="POST" action="{{ route('news.destroy', $news) }}" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            @endif
        @endauth
    </div>

    <div class="card">
        <div style="color: var(--text-gray); font-size: 0.8rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.03em; margin-bottom: 0.5rem;">
            {{ $news->publication_date->format('F j, Y') }}
        </div>
        <h1 style="margin-bottom: 1rem;">{{ $news->title }}</h1>

        @if($news->image)
            <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}" style="max-width: 100%; height: auto; border: 2px solid var(--ink); margin-bottom: 1.25rem; display: block;">
        @endif

        <div style="line-height: 1.8;">
            {!! nl2br(e($news->content)) !!}
        </div>
    </div>

    <div class="card">
        <h2>Comments ({{ $news->comments->count() }})</h2>

        @auth
            <form method="POST" action="{{ route('news.comments.store', $news) }}" style="margin-bottom: 1.5rem;">
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
        @else
            <div class="card" style="margin-bottom: 1.5rem;">
                <p style="margin: 0; color: var(--text-gray);">
                    <a href="{{ route('login') }}" style="color: var(--ink); font-weight: 800;">Login</a> or
                    <a href="{{ route('register') }}" style="color: var(--ink); font-weight: 800;">Register</a> to leave a comment.
                </p>
            </div>
        @endauth

        @if($news->comments->isEmpty())
            <div class="empty">
                <p>No comments yet. Be the first to comment!</p>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($news->comments as $comment)
                    <div class="comment-item">
                        <div class="comment-header">
                            <div>
                                <a href="{{ route('profile.show', $comment->user) }}" class="comment-author">{{ $comment->user->username ?? $comment->user->name }}</a>
                                <span class="comment-time">{{ $comment->created_at->format('M j, Y \a\t g:i A') }}</span>
                            </div>
                            @auth
                                @if(auth()->user()->is_admin || auth()->id() === $comment->user_id)
                                    <form method="POST" action="{{ route('news.comments.destroy', $comment) }}" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.7rem;">Delete</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                        <div style="line-height: 1.6;">
                            {!! nl2br(e($comment->content)) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
