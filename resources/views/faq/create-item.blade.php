@extends('layouts.app')

@section('title', 'Create FAQ Item')

@push('styles')
<style>
    .form-header {
        margin-bottom: 2rem;
    }
    .form-header h1 {
        margin: 0;
        color: var(--dark-blue);
        font-size: 2rem;
        font-weight: 700;
    }
    .form-card {
        background: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 2rem;
        max-width: 700px;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--dark-blue);
        font-weight: 600;
        font-size: 0.95rem;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 1rem;
        font-family: inherit;
        transition: border-color 0.2s;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--dark-blue-light);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .form-group textarea {
        resize: vertical;
        min-height: 150px;
        line-height: 1.6;
    }
    .form-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
    }
    .form-actions .btn {
        padding: 0.75rem 1.5rem;
    }
</style>
@endpush

@section('content')
    <div class="form-header">
        <h1>Create FAQ Item</h1>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('faq.item.store') }}">
            @csrf

            <div class="form-group">
                <label for="faq_category_id">Category *</label>
                <select id="faq_category_id" name="faq_category_id" required>
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('faq_category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="question">Question *</label>
                <input type="text" id="question" name="question" value="{{ old('question') }}" required placeholder="Enter the question">
            </div>

            <div class="form-group">
                <label for="answer">Answer *</label>
                <textarea id="answer" name="answer" required placeholder="Enter the answer">{{ old('answer') }}</textarea>
            </div>

            <div class="form-group">
                <label for="order">Display Order</label>
                <input type="number" id="order" name="order" value="{{ old('order', 0) }}" placeholder="0">
                <div style="font-size: 0.85rem; color: var(--gray); margin-top: 0.25rem;">Lower numbers appear first within the category</div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create FAQ Item</button>
                <a href="{{ route('faq.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
