<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit News</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 600px; margin: 2rem auto; padding: 0 1rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        input, textarea { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { min-height: 200px; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #1b1b18; color: white; text-decoration: none; border: none; border-radius: 4px; cursor: pointer; }
        .btn-danger { background: #dc3545; }
    </style>
</head>
<body>
    @include('partials.admin-nav')

    <h1>Edit News</h1>

    <form method="POST" action="{{ route('news.update', $news) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" value="{{ old('title', $news->title) }}" required>
        </div>

        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            @if($news->image)
                <p>Current: <img src="{{ asset('storage/' . $news->image) }}" style="max-width: 200px; margin-top: 0.5rem;"></p>
            @endif
        </div>

        <div class="form-group">
            <label for="content">Content *</label>
            <textarea id="content" name="content" required>{{ old('content', $news->content) }}</textarea>
        </div>

        <div class="form-group">
            <label for="publication_date">Publication Date</label>
            <input type="datetime-local" id="publication_date" name="publication_date" value="{{ old('publication_date', $news->publication_date?->format('Y-m-d\TH:i')) }}">
        </div>

        <button type="submit" class="btn">Update News</button>
        <a href="{{ route('news.index') }}" style="margin-left: 1rem;">Cancel</a>
    </form>

    <form method="POST" action="{{ route('news.destroy', $news) }}" style="margin-top: 2rem;" onsubmit="return confirm('Are you sure?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete News</button>
    </form>
</body>
</html>

