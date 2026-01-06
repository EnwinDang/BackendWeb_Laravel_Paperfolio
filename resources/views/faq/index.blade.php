@extends('layouts.app')

@section('title', 'FAQ')

@section('content')
    <h1>Frequently Asked Questions</h1>
    
    @auth
        @if(auth()->user()->is_admin)
            <div style="margin-bottom: 1rem;">
                <a href="{{ route('faq.category.create') }}" class="btn btn-primary">Create Category</a>
                <a href="{{ route('faq.item.create') }}" class="btn btn-primary" style="margin-left: 0.5rem;">Create FAQ Item</a>
            </div>
        @endif
    @endauth

    @if($categories->isEmpty())
        <div class="card">
            <div class="empty">
                <p>No FAQ items yet.</p>
            </div>
        </div>
    @else
        @foreach($categories as $category)
            <div class="card" style="margin-bottom: 1.5rem;">
                <h2 style="border-bottom: 2px solid var(--dark-blue); padding-bottom: 0.5rem; margin-bottom: 1rem;">{{ $category->name }}</h2>
                @if($category->items->isEmpty())
                    <p style="color: var(--gray);">No questions in this category.</p>
                @else
                    @foreach($category->items as $item)
                        <div style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0;">
                            <div style="font-weight: bold; margin-bottom: 0.5rem; color: var(--dark-blue);">{{ $item->question }}</div>
                            <div style="color: var(--gray); line-height: 1.6;">{{ $item->answer }}</div>
                            @auth
                                @if(auth()->user()->is_admin)
                                    <div style="margin-top: 0.5rem;">
                                        <a href="{{ route('faq.item.edit', $item) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Edit</a>
                                        <form method="POST" action="{{ route('faq.item.destroy', $item) }}" style="display: inline;" onsubmit="return confirm('Delete this FAQ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Delete</button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    @endforeach
                @endif
                @auth
                    @if(auth()->user()->is_admin)
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                            <a href="{{ route('faq.category.edit', $category) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Edit Category</a>
                            <form method="POST" action="{{ route('faq.category.destroy', $category) }}" style="display: inline;" onsubmit="return confirm('Delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Delete Category</button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        @endforeach
    @endif
@endsection
