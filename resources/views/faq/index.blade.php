@extends('layouts.app')

@section('title', 'FAQ')

@push('styles')
<style>
    .faq-header {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .faq-header h1 {
        margin: 0;
        color: var(--dark-blue);
        font-size: 2rem;
        font-weight: 700;
    }
    .admin-actions-header {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .faq-categories {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    .faq-category {
        background: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.5rem;
    }
    .category-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .category-title {
        margin: 0;
        color: var(--dark-blue);
        font-size: 1.5rem;
        font-weight: 700;
    }
    .category-admin-actions {
        display: flex;
        gap: 0.5rem;
    }
    .category-admin-actions .btn {
        padding: 0.4rem 0.75rem;
        font-size: 0.85rem;
    }
    .faq-items {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .faq-item {
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .faq-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .faq-question {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark-blue);
        margin-bottom: 0.75rem;
        line-height: 1.4;
    }
    .faq-answer {
        color: var(--gray-dark);
        line-height: 1.7;
        margin-bottom: 0.75rem;
        white-space: pre-wrap;
    }
    .item-admin-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }
    .item-admin-actions .btn {
        padding: 0.35rem 0.65rem;
        font-size: 0.8rem;
    }
    .empty-category {
        text-align: center;
        padding: 2rem;
        color: var(--gray);
        font-style: italic;
    }
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
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
    @media (max-width: 768px) {
        .category-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        .admin-actions-header {
            width: 100%;
        }
        .admin-actions-header .btn {
            flex: 1;
        }
    }
</style>
@endpush

@section('content')
    <div class="faq-header">
        <h1>Frequently Asked Questions</h1>
        @auth
            @if(auth()->user()->is_admin)
                <div class="admin-actions-header">
                    <a href="{{ route('faq.category.create') }}" class="btn btn-primary">Create Category</a>
                    <a href="{{ route('faq.item.create') }}" class="btn btn-primary">Create FAQ Item</a>
                </div>
            @endif
        @endauth
    </div>

    @if($categories->isEmpty())
        <div class="card">
            <div class="empty-state">
                <h2>No FAQ items yet</h2>
                <p>Get started by creating your first FAQ category and items.</p>
                @auth
                    @if(auth()->user()->is_admin)
                        <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                            <a href="{{ route('faq.category.create') }}" class="btn btn-primary">Create Category</a>
                            <a href="{{ route('faq.item.create') }}" class="btn btn-primary">Create FAQ Item</a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    @else
        <div class="faq-categories">
            @foreach($categories as $category)
                <div class="faq-category">
                    <div class="category-header">
                        <h2 class="category-title">{{ $category->name }}</h2>
                        @auth
                            @if(auth()->user()->is_admin)
                                <div class="category-admin-actions">
                                    <a href="{{ route('faq.category.edit', $category) }}" class="btn btn-secondary">Edit Category</a>
                                    <form method="POST" action="{{ route('faq.category.destroy', $category) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this category? All items in this category will also be deleted.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete Category</button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>

                    @if($category->items->isEmpty())
                        <div class="empty-category">
                            <p>No questions in this category yet.</p>
                            @auth
                                @if(auth()->user()->is_admin)
                                    <a href="{{ route('faq.item.create') }}" class="btn btn-secondary" style="margin-top: 0.75rem; padding: 0.4rem 0.75rem; font-size: 0.85rem;">Add FAQ Item</a>
                                @endif
                            @endauth
                        </div>
                    @else
                        <div class="faq-items">
                            @foreach($category->items as $item)
                                <div class="faq-item">
                                    <div class="faq-question">{{ $item->question }}</div>
                                    <div class="faq-answer">{{ $item->answer }}</div>
                                    @auth
                                        @if(auth()->user()->is_admin)
                                            <div class="item-admin-actions">
                                                <a href="{{ route('faq.item.edit', $item) }}" class="btn btn-secondary">Edit</a>
                                                <form method="POST" action="{{ route('faq.item.destroy', $item) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this FAQ item?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection
