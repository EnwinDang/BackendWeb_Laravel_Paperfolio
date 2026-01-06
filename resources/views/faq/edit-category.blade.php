@extends('layouts.app')

@section('title', 'Edit FAQ Category')

@section('content')
    <h1>Edit FAQ Category</h1>

    <div class="card">
        <form method="POST" action="{{ route('faq.category.update', $faqCategory) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Category Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $faqCategory->name) }}" required>
            </div>

            <div class="form-group">
                <label for="order">Order</label>
                <input type="number" id="order" name="order" value="{{ old('order', $faqCategory->order) }}">
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="{{ route('faq.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
