<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>News</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .nav { margin-bottom: 2rem; border-bottom: 1px solid #eee; padding-bottom: 1rem; }
        .nav a { color: #666; text-decoration: none; margin-right: 1rem; }
        .news-item { margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid #eee; }
        .news-item:last-child { border-bottom: none; }
        .news-item h2 { margin: 0 0 0.5rem 0; }
        .news-item img { max-width: 100%; height: auto; margin: 1rem 0; }
        .news-date { color: #666; font-size: 0.875rem; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #1b1b18; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ route('faq.index') }}">FAQ</a>
        <a href="{{ route('contact.show') }}">Contact</a>
        @auth
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        @else
            <a href="{{ route('login') }}">Login</a>
        @endauth
    </div>

    @include('partials.admin-nav')

    <h1>Latest News</h1>
    
    @auth
        @if(auth()->user()->is_admin)
            <div style="margin-bottom: 1rem;">
                <a href="{{ route('news.create') }}" class="btn">Create News</a>
            </div>
        @endif
    @endauth

    @if($news->isEmpty())
        <p>No news items yet.</p>
    @else
        @foreach($news as $item)
            <div class="news-item">
                <h2><a href="{{ route('news.show', $item) }}" style="text-decoration: none; color: inherit;">{{ $item->title }}</a></h2>
                <div class="news-date">{{ $item->publication_date->format('F j, Y') }}</div>
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}">
                @endif
                <p>{{ Str::limit(strip_tags($item->content), 200) }}</p>
                <a href="{{ route('news.show', $item) }}">Read more →</a>
            </div>
        @endforeach
    @endif
</body>
</html>

