<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - {{ config('app.name', 'Laravel') }}</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        h1, h2 {
            margin-bottom: 1rem;
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
        .section {
            margin-bottom: 3rem;
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
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #1b1b18;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
        }
        .btn:hover {
            background-color: #000;
        }
        .btn-buy {
            background-color: #28a745;
        }
        .btn-buy:hover {
            background-color: #218838;
        }
        .btn-sell {
            background-color: #dc3545;
        }
        .btn-sell:hover {
            background-color: #c82333;
        }
        .trade-form {
            display: inline-block;
            margin-left: 0.5rem;
        }
        .trade-form input {
            width: 100px;
            padding: 0.25rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 0 0.25rem;
        }
        .percent-buttons {
            display: flex;
            gap: 0.25rem;
            margin-top: 0.25rem;
            flex-wrap: wrap;
        }
        .percent-btn {
            padding: 0.125rem 0.5rem;
            font-size: 0.75rem;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }
        .percent-btn:hover {
            background-color: #e0e0e0;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .price {
            font-family: monospace;
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
        <a href="{{ route('portfolio.index') }}">Portfolio</a>
        <a href="{{ route('assets.index') }}">All Assets</a>
        @if(auth()->user()->is_admin)
            <a href="{{ route('assets.create') }}">Add Asset</a>
        @endif
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <h1>Dashboard - Welcome, {{ auth()->user()->name }}!</h1>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="error">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!auth()->user()->is_admin)
        <div class="section">
            <h2>My Portfolio</h2>
        @if(count($portfolio) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Asset</th>
                        <th>Symbol</th>
                        <th>Owned</th>
                        <th>Current Price</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($portfolio as $item)
                        <tr>
                            <td>{{ $item['asset']->name }}</td>
                            <td><strong>{{ $item['asset']->symbol }}</strong></td>
                            <td class="price">{{ number_format($item['owned'], 8) }}</td>
                            <td class="price">
                                @if($item['asset']->price)
                                    ${{ number_format($item['asset']->price, 2) }}
                                @else
                                    <span style="color: #999;">—</span>
                                @endif
                            </td>
                            <td class="price">
                                @if($item['asset']->price)
                                    ${{ number_format($item['owned'] * $item['asset']->price, 2) }}
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
                <p>You don't own any assets yet. Start trading below!</p>
            </div>
        @endif
        </div>
    @endif

    @if(!auth()->user()->is_admin)
        <div class="section">
            <h2>Available Assets</h2>
            @if(count($assetsWithOwned) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Symbol</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Owned</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assetsWithOwned as $item)
                            @php
                                $asset = $item['asset'];
                                $owned = $item['owned'];
                            @endphp
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
                                <td class="price">
                                    @if($owned > 0)
                                        {{ number_format($owned, 8) }}
                                    @else
                                        <span style="color: #999;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($asset->price)
                                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                            <form method="POST" action="{{ route('trades.buy', $asset) }}" class="trade-form" style="display: inline;">
                                                @csrf
                                                <input type="number" name="amount" step="0.00000001" min="0.00000001" placeholder="Amount" required style="width: 120px;">
                                                <button type="submit" class="btn btn-buy">Buy</button>
                                            </form>
                                            @if($owned > 0)
                                                <form method="POST" action="{{ route('trades.sell', $asset) }}" class="trade-form" id="sell-form-{{ $asset->id }}" style="display: inline;">
                                                    @csrf
                                                    <input type="number" name="amount" step="0.00000001" min="0.00000001" max="{{ $owned }}" placeholder="Amount" required style="width: 120px;" id="sell-amount-{{ $asset->id }}">
                                                    <button type="submit" class="btn btn-sell">Sell</button>
                                                    <div class="percent-buttons">
                                                        <button type="button" class="percent-btn" onclick="setSellAmount({{ $asset->id }}, {{ $owned }}, 0.25)">25%</button>
                                                        <button type="button" class="percent-btn" onclick="setSellAmount({{ $asset->id }}, {{ $owned }}, 0.50)">50%</button>
                                                        <button type="button" class="percent-btn" onclick="setSellAmount({{ $asset->id }}, {{ $owned }}, 0.75)">75%</button>
                                                        <button type="button" class="percent-btn" onclick="setSellAmount({{ $asset->id }}, {{ $owned }}, 1.00)">100%</button>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color: #999;">No price</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty">
                    <p>No assets available yet.</p>
                </div>
            @endif
        </div>
    @endif

    @if(!auth()->user()->is_admin)
        <div class="section">
            <h2>Recent Trades</h2>
        @if($trades->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Asset</th>
                        <th>Amount</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trades as $trade)
                        <tr>
                            <td>{{ $trade->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <strong style="color: {{ $trade->type === 'buy' ? '#28a745' : '#dc3545' }}">
                                    {{ strtoupper($trade->type) }}
                                </strong>
                            </td>
                            <td>{{ $trade->asset->symbol }}</td>
                            <td class="price">{{ number_format($trade->amount, 8) }}</td>
                            <td class="price">${{ number_format($trade->price_snapshot, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">
                <p>No trades yet.</p>
            </div>
        @endif
        </div>
    @endif

    <script>
        function setSellAmount(assetId, owned, percentage) {
            const amount = owned * percentage;
            const input = document.getElementById('sell-amount-' + assetId);
            if (input) {
                input.value = amount.toFixed(8);
            }
        }
    </script>
</body>
</html>

