@extends('layouts.app')

@section('title', 'Create FAQ Category')

@section('content')
    <h1>Create FAQ Category</h1>

    <div class="card">
        <form method="POST" action="{{ route('faq.category.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Category Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="order">Order</label>
                <input type="number" id="order" name="order" value="{{ old('order', 0) }}">
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Create Category</button>
                <a href="{{ route('faq.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
