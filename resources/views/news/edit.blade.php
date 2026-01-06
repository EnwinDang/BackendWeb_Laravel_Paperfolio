@extends('layouts.app')

@section('title', 'Edit News')

@section('content')
    <h1>Edit News</h1>

    <div class="card">
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
                    <p style="margin-top: 0.5rem;">Current: <img src="{{ asset('storage/' . $news->image) }}" style="max-width: 200px; border-radius: 8px;"></p>
                @endif
            </div>

            <div class="form-group">
                <label for="content">Content *</label>
                <textarea id="content" name="content" required style="min-height: 200px;">{{ old('content', $news->content) }}</textarea>
            </div>

            <div class="form-group">
                <label for="publication_date">Publication Date</label>
                <input type="datetime-local" id="publication_date" name="publication_date" value="{{ old('publication_date', $news->publication_date?->format('Y-m-d\TH:i')) }}">
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Update News</button>
                <a href="{{ route('news.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <div class="card" style="margin-top: 2rem; border: 2px solid var(--error);">
        <h3 style="color: var(--error);">Danger Zone</h3>
        <form method="POST" action="{{ route('news.destroy', $news) }}" onsubmit="return confirm('Are you sure you want to delete this news item?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete News</button>
        </form>
    </div>
@endsection
