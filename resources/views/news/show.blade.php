<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $news->title }}</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .nav { margin-bottom: 2rem; }
        .nav a { color: #666; text-decoration: none; margin-right: 1rem; }
        .news-date { color: #666; font-size: 0.875rem; margin-bottom: 1rem; }
        .news-content img { max-width: 100%; height: auto; margin: 1rem 0; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #1b1b18; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="{{ route('news.index') }}">← Back to News</a>
        @auth
            @if(auth()->user()->is_admin)
                <a href="{{ route('news.edit', $news) }}" class="btn">Edit</a>
            @endif
        @endauth
    </div>

    @include('partials.admin-nav')

    <h1>{{ $news->title }}</h1>
    <div class="news-date">{{ $news->publication_date->format('F j, Y') }}</div>

    @if($news->image)
        <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}">
    @endif

    <div class="news-content">
        {!! nl2br(e($news->content)) !!}
    </div>
</body>
</html>

