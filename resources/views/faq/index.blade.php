<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FAQ</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .nav { margin-bottom: 2rem; border-bottom: 1px solid #eee; padding-bottom: 1rem; }
        .nav a { color: #666; text-decoration: none; margin-right: 1rem; }
        .category { margin-bottom: 3rem; }
        .category h2 { border-bottom: 2px solid #333; padding-bottom: 0.5rem; }
        .faq-item { margin-bottom: 1.5rem; }
        .faq-question { font-weight: bold; margin-bottom: 0.5rem; }
        .faq-answer { color: #666; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #1b1b18; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ route('news.index') }}">News</a>
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

    <h1>Frequently Asked Questions</h1>
    
    @auth
        @if(auth()->user()->is_admin)
            <div style="margin-bottom: 1rem;">
                <a href="{{ route('faq.category.create') }}" class="btn">Create Category</a>
                <a href="{{ route('faq.item.create') }}" class="btn">Create FAQ Item</a>
            </div>
        @endif
    @endauth

    @if($categories->isEmpty())
        <p>No FAQ items yet.</p>
    @else
        @foreach($categories as $category)
            <div class="category">
                <h2>{{ $category->name }}</h2>
                @if($category->items->isEmpty())
                    <p>No questions in this category.</p>
                @else
                    @foreach($category->items as $item)
                        <div class="faq-item">
                            <div class="faq-question">{{ $item->question }}</div>
                            <div class="faq-answer">{{ $item->answer }}</div>
                            @auth
                                @if(auth()->user()->is_admin)
                                    <div style="margin-top: 0.5rem;">
                                        <a href="{{ route('faq.item.edit', $item) }}" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Edit</a>
                                        <form method="POST" action="{{ route('faq.item.destroy', $item) }}" style="display: inline;" onsubmit="return confirm('Delete this FAQ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; background: #dc3545;">Delete</button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    @endforeach
                @endif
                @auth
                    @if(auth()->user()->is_admin)
                        <div style="margin-top: 1rem;">
                            <a href="{{ route('faq.category.edit', $category) }}" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Edit Category</a>
                            <form method="POST" action="{{ route('faq.category.destroy', $category) }}" style="display: inline;" onsubmit="return confirm('Delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; background: #dc3545;">Delete Category</button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        @endforeach
    @endif
</body>
</html>

