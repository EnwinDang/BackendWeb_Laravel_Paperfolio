@extends(auth()->check() ? (auth()->user()->is_admin ? 'layouts.app' : 'layouts.dashboard') : 'layouts.marketing')

@section('title', 'Announcements')

@push('styles')
<style>
    .news-page-header {
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .news-page-header p {
        color: var(--text-gray);
        font-size: 0.9rem;
        margin: 0.25rem 0 0;
    }
    .news-stats {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .stat-card {
        background: var(--card-bg);
        border: 2px solid var(--ink);
        box-shadow: var(--shadow-sm);
        padding: 1rem 1.25rem;
        flex: 1;
        min-width: 150px;
    }
    .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 700;
        color: var(--text-gray);
        margin-bottom: 0.25rem;
    }
    .stat-value {
        font-size: 1.6rem;
        font-weight: 800;
    }
    .search-section {
        margin-bottom: 1.5rem;
        background: var(--card-bg);
        border: 2px solid var(--ink);
        box-shadow: var(--shadow-sm);
        padding: 1.1rem 1.25rem;
    }
    .search-form {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }
    .search-results-info {
        margin-bottom: 1.25rem;
        color: var(--text-gray);
        font-size: 0.85rem;
        font-weight: 600;
    }
    .news-feed {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .news-card {
        background: var(--card-bg);
        border: 2px solid var(--ink);
        box-shadow: var(--shadow);
        display: block;
        text-decoration: none;
        color: inherit;
        position: relative;
        transition: transform 0.1s, box-shadow 0.1s;
    }
    .news-card:hover {
        transform: translate(-3px, -3px);
        box-shadow: 7px 7px 0 var(--ink);
    }
    .news-card-new-badge {
        position: absolute;
        top: -12px;
        left: 1.25rem;
        background: var(--accent);
        color: var(--on-accent);
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.2rem 0.6rem;
        border: 2px solid var(--ink);
        z-index: 1;
    }
    .delete-button-top {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 2;
    }
    .delete-button-top .btn {
        padding: 0.4rem 0.75rem;
        font-size: 0.75rem;
    }
    .news-card-content-wrapper {
        display: flex;
        gap: 1.5rem;
        padding: 1.5rem;
    }
    .news-card-image-container {
        flex-shrink: 0;
        width: 200px;
        height: 150px;
        overflow: hidden;
        background: var(--page-bg);
        border: 2px solid var(--ink);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .news-card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .news-card-content {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
    }
    .news-card-meta {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 0.6rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        font-weight: 700;
        color: var(--text-gray);
        flex-wrap: wrap;
    }
    .news-card-title {
        margin: 0 0 0.6rem 0;
        font-size: 1.3rem;
        font-weight: 800;
        line-height: 1.25;
    }
    .news-card-excerpt {
        color: var(--text-gray);
        font-size: 0.9rem;
        line-height: 1.6;
        margin: 0;
        flex: 1;
    }
    .news-card-readmore {
        margin-top: 0.75rem;
        font-weight: 800;
        font-size: 0.8rem;
        text-transform: uppercase;
        color: var(--ink);
    }
    .news-card:hover .news-card-readmore {
        color: var(--accent);
    }
    .admin-actions {
        display: flex;
        gap: 0.5rem;
        padding: 0 1.5rem 1.5rem 1.5rem;
        border-top: 2px solid var(--ink);
        margin-top: 0;
        padding-top: 1rem;
    }
    @media (max-width: 768px) {
        .news-card-content-wrapper {
            flex-direction: column;
        }
        .news-card-image-container {
            width: 100%;
            height: 180px;
        }
        .news-card-title {
            font-size: 1.1rem;
        }
        .news-page-header .header-actions {
            width: 100%;
        }
        .news-page-header .header-actions .btn {
            flex: 1;
        }
    }
</style>
@endpush

@section('content')
    <div class="news-page-header">
        <div>
            <h1 style="margin-bottom: 0.25rem;">Announcements</h1>
            <p>Official updates from the PaperFolio team — new features, new assets, and platform changes.</p>
        </div>
        @auth
            @if(auth()->user()->is_admin)
                <div class="header-actions">
                    <a href="{{ route('news.create') }}" class="btn btn-primary">+ Post Announcement</a>
                </div>
            @endif
        @endauth
    </div>

    @auth
        @if(auth()->user()->is_admin)
            <div class="news-stats">
                <div class="stat-card">
                    <div class="stat-label">Total Announcements</div>
                    <div class="stat-value">{{ \App\Models\News::count() }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">This Month</div>
                    <div class="stat-value">{{ \App\Models\News::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count() }}</div>
                </div>
            </div>
        @endif
    @endauth

    <div class="search-section">
        <form method="GET" action="{{ route('news.index') }}" class="search-form">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search announcements by title..."
            >
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('news.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>

    @if($news->isEmpty())
        <div class="card">
            <div class="empty">
                @if(request('search'))
                    <h2>No Results Found</h2>
                    <p>No announcements found matching "{{ request('search') }}".</p>
                    <a href="{{ route('news.index') }}" class="btn btn-primary">View All Announcements</a>
                @else
                    <h2>No Announcements Yet</h2>
                    <p>Get started by posting your first platform update.</p>
                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('news.create') }}" class="btn btn-primary">Post Announcement</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    @else
        @if(request('search'))
            <div class="search-results-info">
                Found {{ $news->count() }} {{ \Illuminate\Support\Str::plural('result', $news->count()) }} for "{{ request('search') }}"
            </div>
        @endif

        <div class="news-feed">
            @foreach($news as $item)
                <div class="news-card">
                    @if($item->publication_date->greaterThanOrEqualTo(now()->subDays(7)))
                        <span class="news-card-new-badge">New</span>
                    @endif
                    @auth
                        @if(auth()->user()->is_admin)
                            <div class="delete-button-top" onclick="event.stopPropagation();">
                                <form method="POST" action="{{ route('news.destroy', $item) }}" style="display: inline;" onsubmit="return confirmAction(event, 'Are you sure you want to delete this announcement? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        @endif
                    @endauth
                    <a href="{{ route('news.show', $item) }}" style="text-decoration: none; color: inherit; display: block;">
                        <div class="news-card-content-wrapper">
                            @if($item->image)
                                <div class="news-card-image-container">
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="news-card-image">
                                </div>
                            @endif
                            <div class="news-card-content">
                                <div class="news-card-meta">
                                    <span>{{ $item->publication_date->format('F j, Y') }}</span>
                                    @auth
                                        @if(auth()->user()->is_admin && $item->updated_at != $item->created_at)
                                            <span>&middot;</span>
                                            <span>Updated {{ $item->updated_at->format('M j, Y') }}</span>
                                        @endif
                                    @endauth
                                </div>
                                <h2 class="news-card-title">{{ $item->title }}</h2>
                                <p class="news-card-excerpt">{{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($item->content), 150) }}</p>
                                <div class="news-card-readmore">Read more &rarr;</div>
                            </div>
                        </div>
                    </a>

                    @auth
                        @if(auth()->user()->is_admin)
                            <div class="admin-actions">
                                <a href="{{ route('news.edit', $item) }}" class="btn btn-primary">Edit</a>
                            </div>
                        @endif
                    @endauth
                </div>
            @endforeach
        </div>
    @endif
@endsection
