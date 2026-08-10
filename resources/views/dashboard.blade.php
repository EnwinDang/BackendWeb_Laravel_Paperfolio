@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    @if(!auth()->user()->is_admin)
        @php
            $totalHoldingsValue = collect($portfolio)->sum(fn($item) => $item['asset']->price ? $item['owned'] * $item['asset']->price : 0);
            $totalPositionsValue = $openPositions->sum(fn($position) => $position->asset->price
                ? (float) $position->margin_usd + $position->unrealizedPnl((float) $position->asset->price)
                : (float) $position->margin_usd);
            $totalAccountValue = $cashBalance + $totalHoldingsValue + $totalPositionsValue;
        @endphp
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="font-size: 1.9rem; font-weight: 800;">${{ number_format($totalAccountValue, 2) }}</div>
                    <div style="color: var(--text-gray); font-size: 0.85rem; margin-top: 0.25rem;">Cash + holdings, welcome back {{ auth()->user()->getDisplayName() }}</div>
                </div>
                <a href="{{ route('portfolio.index') }}" class="btn btn-secondary">Manage assets</a>
            </div>
        </div>

        @if($openPositions->count() > 0)
            <div class="card">
                <h2>Open Positions</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Market</th>
                            <th>Side</th>
                            <th>Leverage</th>
                            <th>Margin</th>
                            <th>Entry Price</th>
                            <th>Mark Price</th>
                            <th>PnL (ROE)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($openPositions as $position)
                            @php
                                $markPrice = (float) $position->asset->price;
                                $pnl = $position->unrealizedPnl($markPrice);
                                $roe = $position->roePercent($markPrice);
                            @endphp
                            <tr>
                                <td><a href="{{ route('assets.show', $position->asset) }}" style="color: inherit;"><strong>{{ $position->asset->symbol }}/USD</strong></a></td>
                                <td><span class="pill {{ $position->direction === 'long' ? 'pill-long' : 'pill-short' }}">{{ ucfirst($position->direction) }}</span></td>
                                <td>{{ $position->leverage }}x</td>
                                <td class="price">${{ number_format($position->margin_usd, 2) }}</td>
                                <td class="price">${{ number_format($position->entry_price, 2) }}</td>
                                <td class="price">${{ number_format($markPrice, 2) }}</td>
                                <td class="price" style="color: {{ $pnl >= 0 ? 'var(--success)' : 'var(--error)' }};">
                                    {{ $pnl >= 0 ? '+' : '' }}${{ number_format($pnl, 2) }} ({{ $roe >= 0 ? '+' : '' }}{{ number_format($roe, 1) }}%)
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('positions.close', $position) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary">Close</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if(isset($watchedAssets) && $watchedAssets->count() > 0)
            <div class="card">
                <h2>Watchlist</h2>
                <div class="watch-grid">
                    @foreach($watchedAssets as $asset)
                        @php $change = $asset->price_change_24h !== null ? (float) $asset->price_change_24h : null; @endphp
                        <div class="watch-card">
                            <a href="{{ route('assets.show', $asset) }}" style="color: inherit; text-decoration: none; display: block;">
                                <div class="watch-card-head">
                                    <span>{{ $asset->name }}</span>
                                    <span class="watch-card-symbol">{{ $asset->symbol }}</span>
                                </div>
                                <div style="display: flex; align-items: baseline; gap: 0.6rem; flex-wrap: wrap;">
                                    <div class="watch-card-price">
                                        @if($asset->price)
                                            ${{ number_format($asset->price, 2) }}
                                        @else
                                            <span style="color: var(--text-gray); font-size: 1rem;">No price yet</span>
                                        @endif
                                    </div>
                                    @if($change !== null)
                                        <span style="font-size: 0.8rem; font-weight: 700; color: {{ $change >= 0 ? 'var(--success)' : 'var(--error)' }};">
                                            {{ $change >= 0 ? '▲' : '▼' }} {{ number_format(abs($change), 2) }}%
                                        </span>
                                    @endif
                                </div>
                            </a>
                            <form method="POST" action="{{ route('assets.watchlist.remove', $asset) }}">
                                @csrf
                                <button type="submit" class="watch-card-remove">Remove from watchlist</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

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
                            <th></th>
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
                                <td>
                                    <a href="{{ route('assets.show', $item['asset']) }}" class="btn btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.85rem;">Trade</a>
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
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assetsWithOwned as $item)
                            @php
                                $asset = $item['asset'];
                                $owned = $item['owned'];
                            @endphp
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <a href="{{ route('assets.show', $asset) }}" style="color: inherit; text-decoration: none;"><strong>{{ $asset->symbol }}</strong></a>
                                        @if($item['is_watched'] ?? false)
                                            <form method="POST" action="{{ route('assets.watchlist.remove', $asset) }}" style="display: inline;" title="Remove from watchlist">
                                                @csrf
                                                <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0.25rem; color: #f59e0b; font-size: 1.1rem;" title="Remove from watchlist">⭐</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('assets.watchlist.add', $asset) }}" style="display: inline;" title="Add to watchlist">
                                                @csrf
                                                <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0.25rem; color: var(--gray); font-size: 1.1rem;" title="Add to watchlist">☆</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
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
                                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-primary" style="padding: 0.4rem 1rem; font-size: 0.85rem;">Trade</a>
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
@endsection
