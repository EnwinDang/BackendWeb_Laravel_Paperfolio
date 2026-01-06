@extends('layouts.app')

@section('title', 'News')

@section('content')
    <h1>Latest News</h1>
    
    @auth
        @if(auth()->user()->is_admin)
            <div style="margin-bottom: 1rem;">
                <a href="{{ route('news.create') }}" class="btn btn-primary">Create News</a>
            </div>
        @endif
    @endauth

    @if($news->isEmpty())
        <div class="card">
            <div class="empty">
                <p>No news items yet.</p>
            </div>
        </div>
    @else
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
                <p>{{ Str::limit(strip_tags($item->content), 200) }}</p>
                <a href="{{ route('news.show', $item) }}" class="btn btn-primary">Read more →</a>
            </div>
        @endforeach
    @endif
@endsection
