@extends('layouts.app')

@section('title', 'News')

@push('styles')
<style>
    .news-page-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .news-page-header h1 {
        margin: 0;
        color: var(--dark-blue);
        font-size: 2rem;
        font-weight: 700;
    }
    .header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .search-section {
        margin-bottom: 2rem;
        background: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.5rem;
    }
    .search-form {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }
    .search-input {
        flex: 1;
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 1rem;
        transition: border-color 0.2s;
    }
    .search-input:focus {
        outline: none;
        border-color: var(--dark-blue-light);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .search-results-info {
        margin-bottom: 1.5rem;
        color: var(--gray);
        font-size: 0.95rem;
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border-radius: 6px;
    }
    .news-feed-container {
        max-width: 900px;
        margin: 0 auto;
    }
    .news-feed {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .news-card {
        background: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        transition: box-shadow 0.2s, border-color 0.2s;
        display: block;
        text-decoration: none;
        color: inherit;
        position: relative;
    }
    .news-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-color: var(--dark-blue-light);
    }
    .delete-button-top {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 10;
    }
    .delete-button-top .btn {
        padding: 0.4rem 0.75rem;
        font-size: 0.8rem;
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
        background: #f8fafc;
        border-radius: 6px;
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
    }
    .news-card-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.75rem;
        font-size: 0.85rem;
        color: var(--gray);
        flex-wrap: wrap;
    }
    .news-card-date {
        font-weight: 500;
    }
    .news-card-title {
        margin: 0 0 0.75rem 0;
        color: var(--dark-blue);
        font-size: 1.35rem;
        font-weight: 700;
        line-height: 1.3;
    }
    .news-card-excerpt {
        color: var(--gray-dark);
        font-size: 0.95rem;
        line-height: 1.6;
        margin: 0 0 1rem 0;
    }
    .admin-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }
    .admin-actions .btn {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }
    .empty-state h2 {
        color: var(--dark-blue);
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
    .empty-state p {
        color: var(--gray);
        font-size: 1rem;
        margin-bottom: 1.5rem;
    }
    .news-stats {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .stat-card {
        background: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 1rem 1.5rem;
        flex: 1;
        min-width: 150px;
    }
    .stat-label {
        font-size: 0.85rem;
        color: var(--gray);
        margin-bottom: 0.25rem;
    }
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-blue);
    }
    @media (max-width: 768px) {
        .news-card-content-wrapper {
            flex-direction: column;
        }
        .news-card-image-container {
            width: 100%;
            height: 200px;
        }
        .news-card-title {
            font-size: 1.15rem;
        }
        .header-actions {
            width: 100%;
        }
        .header-actions .btn {
            flex: 1;
        }
    }
</style>
@endpush

@section('content')
    <div class="news-page-header">
        <h1>Latest News</h1>
        @auth
            @if(auth()->user()->is_admin)
                <div class="header-actions">
                    <a href="{{ route('news.create') }}" class="btn btn-primary">Create News</a>
                </div>
            @endif
        @endauth
    </div>
    
    <div class="news-feed-container">
        @auth
            @if(auth()->user()->is_admin)
                <div class="news-stats">
                    <div class="stat-card">
                        <div class="stat-label">Total News</div>
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
                    placeholder="Search news by title..." 
                    class="search-input"
                >
                <button type="submit" class="btn btn-primary">Search</button>
                @if(request('search'))
                    <a href="{{ route('news.index') }}" class="btn btn-secondary">Clear</a>
                @endif
            </form>
        </div>

        @if($news->isEmpty())
            <div class="empty-state">
                @if(request('search'))
                    <h2>No Results Found</h2>
                    <p>No news items found matching "{{ request('search') }}".</p>
                    <a href="{{ route('news.index') }}" class="btn btn-primary">View All News</a>
                @else
                    <h2>No News Items Yet</h2>
                    <p>Get started by creating your first news article.</p>
                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('news.create') }}" class="btn btn-primary">Create News</a>
                        @endif
                    @endauth
                @endif
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
                        @auth
                            @if(auth()->user()->is_admin)
                                <div class="delete-button-top" onclick="event.stopPropagation();">
                                    <form method="POST" action="{{ route('news.destroy', $item) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this news item? This action cannot be undone.');">
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
                                        <span class="news-card-date">{{ $item->publication_date->format('F j, Y') }}</span>
                                        @auth
                                            @if(auth()->user()->is_admin)
                                                <span>•</span>
                                                <span>Created: {{ $item->created_at->format('M j, Y') }}</span>
                                                @if($item->updated_at != $item->created_at)
                                                    <span>•</span>
                                                    <span>Updated: {{ $item->updated_at->format('M j, Y') }}</span>
                                                @endif
                                            @endif
                                        @endauth
                                    </div>
                                    <h2 class="news-card-title">{{ $item->title }}</h2>
                                    <p class="news-card-excerpt">{{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($item->content), 150) }}</p>
                                </div>
                            </div>
                        </a>
                        
                        @auth
                            @if(auth()->user()->is_admin)
                                <div class="admin-actions" style="padding: 0 1.5rem 1.5rem 1.5rem;">
                                    <a href="{{ route('news.show', $item) }}" class="btn btn-secondary">View</a>
                                    <a href="{{ route('news.edit', $item) }}" class="btn btn-primary">Edit</a>
                                </div>
                            @endif
                        @endauth
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
