@extends('layouts.app')

@section('title', 'Edit FAQ Item')

@section('content')
    <h1>Edit FAQ Item</h1>

    <div class="card">
        <form method="POST" action="{{ route('faq.item.update', $faqItem) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="faq_category_id">Category *</label>
                <select id="faq_category_id" name="faq_category_id" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('faq_category_id', $faqItem->faq_category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="question">Question *</label>
                <input type="text" id="question" name="question" value="{{ old('question', $faqItem->question) }}" required>
            </div>

            <div class="form-group">
                <label for="answer">Answer *</label>
                <textarea id="answer" name="answer" required style="min-height: 150px;">{{ old('answer', $faqItem->answer) }}</textarea>
            </div>

            <div class="form-group">
                <label for="order">Order</label>
                <input type="number" id="order" name="order" value="{{ old('order', $faqItem->order) }}">
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Update FAQ Item</button>
                <a href="{{ route('faq.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
