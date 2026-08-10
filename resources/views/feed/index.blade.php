@extends('layouts.dashboard')

@section('title', 'Global Feed')

@push('styles')
<style>
    .post-card {
        border-bottom: 2px solid var(--ink);
        padding: 1rem 0;
    }
    .post-card:last-child {
        border-bottom: none;
    }
    .post-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 0.4rem;
    }
    .post-author {
        font-weight: 800;
        text-decoration: none;
        color: var(--ink);
        text-transform: uppercase;
        font-size: 0.85rem;
    }
    .post-time {
        color: var(--text-gray);
        font-size: 0.75rem;
    }
    .post-content {
        font-size: 0.95rem;
        margin-bottom: 0.6rem;
        white-space: pre-wrap;
    }
    .post-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .like-btn {
        background: none;
        border: 2px solid var(--ink);
        color: var(--ink);
        padding: 0.25rem 0.6rem;
        font-family: inherit;
        font-weight: 700;
        font-size: 0.8rem;
        cursor: pointer;
    }
    .like-btn.liked {
        background: var(--accent);
        color: var(--on-accent);
    }
    .cashtag {
        color: var(--ink);
        font-weight: 800;
        background: var(--accent-dim);
        padding: 0 0.2rem;
        text-decoration: none;
    }
</style>
@endpush

@section('content')
    @if(!auth()->user()->is_admin)
        <div class="card">
            <h2>Share your alpha</h2>
            <form method="POST" action="{{ route('feed.store') }}">
                @csrf
                <textarea name="content" rows="3" maxlength="500" placeholder="What's happening? Use $cashtags like $BTC to link a market..." required style="margin-bottom: 0.75rem;"></textarea>
                <button type="submit" class="btn btn-primary">Post</button>
            </form>
        </div>
    @endif

    <div class="card">
        <h2>Global Feed</h2>
        @if($posts->isEmpty())
            <div class="empty"><p>No posts yet. Be the first to share something.</p></div>
        @else
            @foreach($posts as $post)
                <div class="post-card">
                    <div class="post-header">
                        <a href="{{ route('profile.show', $post->user) }}" class="post-author">{{ $post->user->getDisplayName() }}</a>
                        <span class="post-time">{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="post-content">{!! $post->renderedContent() !!}</div>
                    <div class="post-actions">
                        @auth
                            @php $liked = $post->isLikedBy(auth()->user()); @endphp
                            <form method="POST" action="{{ route($liked ? 'feed.unlike' : 'feed.like', $post) }}">
                                @csrf
                                <button type="submit" class="like-btn {{ $liked ? 'liked' : '' }}">&hearts; {{ $post->likers_count }}</button>
                            </form>
                        @else
                            <span class="like-btn">&hearts; {{ $post->likers_count }}</span>
                        @endauth

                        @auth
                            @if($post->user_id === auth()->id() || auth()->user()->is_admin)
                                <form method="POST" action="{{ route('feed.destroy', $post) }}" onsubmit="return confirm('Delete this post?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="like-btn">Delete</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
