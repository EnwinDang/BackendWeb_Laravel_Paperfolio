<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assets - {{ config('app.name', 'Laravel') }}</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        h1 {
            margin-bottom: 1rem;
        }
        .header-actions {
            margin-bottom: 2rem;
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
        }
        .btn:hover {
            background-color: #000;
        }
        .btn-secondary {
            background-color: #666;
        }
        .btn-secondary:hover {
            background-color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: 600;
        }
        .empty {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
        .price {
            font-family: monospace;
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
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <h1>Assets</h1>

    <div class="header-actions">
        <a href="{{ route('assets.create') }}" class="btn">Add Asset</a>
    </div>

    @if($assets->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Symbol</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $asset)
                    <tr>
                        <td><strong>{{ $asset->symbol }}</strong></td>
                        <td>{{ $asset->name }}</td>
                        <td class="price">
                            @if($asset->price)
                                ${{ number_format($asset->price, 2) }}
                            @else
                                <span style="color: #999;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($asset->price_last_updated_at)
                                {{ $asset->price_last_updated_at->format('Y-m-d H:i') }}
                            @else
                                <span style="color: #999;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">
            <p>No assets yet. <a href="{{ route('assets.create') }}">Create your first asset</a></p>
        </div>
    @endif
</body>
</html>
