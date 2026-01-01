<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit FAQ Item</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 600px; margin: 2rem auto; padding: 0 1rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        input, select, textarea { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { min-height: 150px; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #1b1b18; color: white; text-decoration: none; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    @include('partials.admin-nav')

    <h1>Edit FAQ Item</h1>

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
            <textarea id="answer" name="answer" required>{{ old('answer', $faqItem->answer) }}</textarea>
        </div>

        <div class="form-group">
            <label for="order">Order</label>
            <input type="number" id="order" name="order" value="{{ old('order', $faqItem->order) }}">
        </div>

        <button type="submit" class="btn">Update FAQ Item</button>
        <a href="{{ route('faq.index') }}" style="margin-left: 1rem;">Cancel</a>
    </form>
</body>
</html>

