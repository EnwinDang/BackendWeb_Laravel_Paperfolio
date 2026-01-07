@extends('layouts.app')

@section('title', 'News')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <h1 style="margin: 0;">Latest News</h1>
        @auth
            @if(auth()->user()->is_admin)
                <a href="{{ route('news.create') }}" class="btn btn-primary">Create News</a>
            @endif
        @endauth
    </div>
    
    <div class="card" style="margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('news.index') }}" style="display: flex; gap: 0.5rem; align-items: center;">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Search news by title..." 
                style="flex: 1; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem;"
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
                    <p>No news items found matching "{{ request('search') }}".</p>
                    <a href="{{ route('news.index') }}" class="btn btn-secondary" style="margin-top: 1rem;">View All News</a>
                @else
                    <p>No news items yet.</p>
                @endif
            </div>
        </div>
    @else
        @if(request('search'))
            <div style="margin-bottom: 1rem; color: var(--gray);">
                Found {{ $news->count() }} {{ \Illuminate\Support\Str::plural('result', $news->count()) }} for "{{ request('search') }}"
            </div>
        @endif
        @foreach($news as $item)
            <div class="card" style="margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                    <h2 style="margin: 0; flex: 1;"><a href="{{ route('news.show', $item) }}" style="text-decoration: none; color: inherit;">{{ $item->title }}</a></h2>
                    @auth
                        @if(auth()->user()->is_admin)
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('news.edit', $item) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Edit</a>
                                <form method="POST" action="{{ route('news.destroy', $item) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this news item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Delete</button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
                <div style="color: var(--gray); font-size: 0.875rem; margin-bottom: 1rem;">{{ $item->publication_date->format('F j, Y') }}</div>
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" style="max-width: 100%; height: auto; margin-bottom: 1rem; border-radius: 8px;">
                @endif
                <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->content), 200) }}</p>
                <a href="{{ route('news.show', $item) }}" class="btn btn-primary">Read more →</a>
            </div>
        @endforeach
    @endif
@endsection
