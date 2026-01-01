<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Asset - {{ config('app.name', 'Laravel') }}</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        h1 {
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        input[type="text"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #1b1b18;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn:hover {
            background-color: #000;
        }
        .btn-secondary {
            background-color: #666;
            margin-left: 0.5rem;
        }
        .btn-secondary:hover {
            background-color: #555;
        }
        .nav {
            margin-bottom: 2rem;
        }
        .nav a {
            color: #666;
            text-decoration: none;
            margin-right: 1rem;
        }
        .nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="nav">
        <a href="{{ route('assets.index') }}">← Back to Assets</a>
    </div>

    <h1>Create Asset</h1>

    @if ($errors->any())
        <div style="background-color: #fff3cd; border: 1px solid #ffc107; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('assets.store') }}">
        @csrf

        <div class="form-group">
            <label for="name">Name *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name') }}" 
                required
                placeholder="e.g., Bitcoin"
            >
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="symbol">Symbol *</label>
            <input 
                type="text" 
                id="symbol" 
                name="symbol" 
                value="{{ old('symbol') }}" 
                required
                placeholder="e.g., BTC"
                style="text-transform: uppercase;"
            >
            @error('symbol')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <button type="submit" class="btn">Save Asset</button>
            <a href="{{ route('assets.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</body>
</html>
