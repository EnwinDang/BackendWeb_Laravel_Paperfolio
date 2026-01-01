<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit FAQ Category</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 600px; margin: 2rem auto; padding: 0 1rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        input { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #1b1b18; color: white; text-decoration: none; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    @include('partials.admin-nav')

    <h1>Edit FAQ Category</h1>

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

        <button type="submit" class="btn">Update Category</button>
        <a href="{{ route('faq.index') }}" style="margin-left: 1rem;">Cancel</a>
    </form>
</body>
</html>

