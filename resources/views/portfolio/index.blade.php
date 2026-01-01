<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portfolio - {{ config('app.name', 'Laravel') }}</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
            line-height: 1.6;
            color: #333;
        }
        h1, h2 {
            color: #1b1b18;
            margin-bottom: 1rem;
        }
        .nav {
            margin-bottom: 2rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
        }
        .nav a {
            color: #666;
            text-decoration: none;
            margin-right: 1rem;
        }
        .nav a:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f9f9f9;
            font-weight: 600;
        }
        .price {
            font-family: monospace;
        }
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
            border-top: 2px solid #333;
        }
        .empty {
            text-align: center;
            padding: 2rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="nav">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('assets.index') }}">Assets</a>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <h1>My Portfolio</h1>

    @if(count($portfolio) > 0)
        <table>
            <thead>
                <tr>
                    <th>Asset</th>
                    <th>Holdings</th>
                    <th>Current Price</th>
                    <th>Current Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($portfolio as $item)
                    <tr>
                        <td><strong>{{ $item['asset']->symbol }}</strong> - {{ $item['asset']->name }}</td>
                        <td class="price">{{ number_format($item['holdings'], 8) }}</td>
                        <td class="price">
                            @if($item['current_price'])
                                ${{ number_format($item['current_price'], 2) }}
                            @else
                                <span style="color: #999;">—</span>
                            @endif
                        </td>
                        <td class="price">
                            @if($item['current_value'] !== null)
                                ${{ number_format($item['current_value'], 2) }}
                            @else
                                <span style="color: #999;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3"><strong>Total Portfolio Value</strong></td>
                    <td class="price"><strong>${{ number_format($totalPortfolioValue, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="empty">
            <p>Your portfolio is empty. Start trading to build your portfolio!</p>
        </div>
    @endif
</body>
</html>

