@extends('layouts.app')

@section('title', 'Create FAQ Item')

@section('content')
    <h1>Create FAQ Item</h1>

    <div class="card">
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
                <input type="text" id="question" name="question" value="{{ old('question') }}" required>
            </div>

            <div class="form-group">
                <label for="answer">Answer *</label>
                <textarea id="answer" name="answer" required style="min-height: 150px;">{{ old('answer') }}</textarea>
            </div>

            <div class="form-group">
                <label for="order">Order</label>
                <input type="number" id="order" name="order" value="{{ old('order', 0) }}">
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Create FAQ Item</button>
                <a href="{{ route('faq.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
