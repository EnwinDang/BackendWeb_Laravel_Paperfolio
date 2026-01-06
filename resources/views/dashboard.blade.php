@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard - Welcome, {{ auth()->user()->name }}!</h1>

    @if(!auth()->user()->is_admin)
        <div class="card">
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
                                        <span style="color: var(--gray);">—</span>
                                    @endif
                                </td>
                                <td class="price">
                                    @if($item['asset']->price)
                                        ${{ number_format($item['owned'] * $item['asset']->price, 2) }}
                                    @else
                                        <span style="color: var(--gray);">—</span>
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
        <div class="card">
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
                                        <span style="color: var(--gray);">—</span>
                                    @endif
                                </td>
                                <td class="price">
                                    @if($owned > 0)
                                        {{ number_format($owned, 8) }}
                                    @else
                                        <span style="color: var(--gray);">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($asset->price)
                                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                            <form method="POST" action="{{ route('trades.buy', $asset) }}" class="trade-form">
                                                @csrf
                                                <div style="display: flex; align-items: center; gap: 0.25rem;">
                                                    <span style="color: var(--gray-dark);">$</span>
                                                    <input type="number" name="dollar_amount" step="0.01" min="0.01" placeholder="Dollar amount" required style="flex: 1;">
                                                </div>
                                                <button type="submit" class="btn btn-success">Buy</button>
                                            </form>
                                            @if($owned > 0)
                                                <form method="POST" action="{{ route('trades.sell', $asset) }}" class="trade-form" id="sell-form-{{ $asset->id }}">
                                                    @csrf
                                                    <input type="number" name="amount" step="0.00000001" min="0.00000001" max="{{ $owned }}" placeholder="Amount" required id="sell-amount-{{ $asset->id }}">
                                                    <button type="submit" class="btn btn-danger">Sell</button>
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
                                        <span style="color: var(--gray);">No price</span>
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
        <div class="card">
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
                                    <strong style="color: {{ $trade->type === 'buy' ? 'var(--success)' : 'var(--error)' }}">
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

    @push('scripts')
    <script>
        function setSellAmount(assetId, owned, percentage) {
            const amount = owned * percentage;
            const input = document.getElementById('sell-amount-' + assetId);
            if (input) {
                input.value = amount.toFixed(8);
            }
        }
    </script>
    @endpush
@endsection
