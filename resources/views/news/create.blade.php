@extends('layouts.app')

@section('title', 'Create News')

@section('content')
    <h1>Create News</h1>

    <div class="card">
        <form method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required>
            </div>

            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <div class="form-group">
                <label for="content">Content *</label>
                <textarea id="content" name="content" required style="min-height: 200px;">{{ old('content') }}</textarea>
            </div>

            <div class="form-group">
                <label for="publication_date">Publication Date</label>
                <input type="datetime-local" id="publication_date" name="publication_date" value="{{ old('publication_date') }}">
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Create News</button>
                <a href="{{ route('news.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
