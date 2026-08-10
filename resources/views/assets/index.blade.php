@extends(auth()->check() && auth()->user()->is_admin ? 'layouts.app' : 'layouts.dashboard')

@section('title', 'Crypto Assets')

@section('content')
    @auth
        @if(auth()->user()->is_admin)
            <h1>Assets</h1>
            <div style="margin-bottom: 1rem;">
                <a href="{{ route('assets.create') }}" class="btn btn-primary">Create Asset</a>
            </div>

            @if(count($assetsWithOwned) > 0)
                <div class="card">
                    <table>
                        <thead>
                            <tr>
                                <th>Symbol</th>
                                <th>Name</th>
                                <th>CoinGecko ID</th>
                                <th>Price</th>
                                <th>24h</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assetsWithOwned as $item)
                                @php $asset = $item['asset']; $change = $asset->price_change_24h !== null ? (float) $asset->price_change_24h : null; @endphp
                                <tr>
                                    <td><strong>{{ $asset->symbol }}</strong></td>
                                    <td>{{ $asset->name }}</td>
                                    <td>
                                        @if($asset->coingecko_id)
                                            <code>{{ $asset->coingecko_id }}</code>
                                        @else
                                            <span style="color: var(--gray);">—</span>
                                        @endif
                                    </td>
                                    <td class="price">
                                        @if($asset->price)
                                            ${{ number_format($asset->price, 2) }}
                                        @else
                                            <span style="color: var(--gray);">—</span>
                                        @endif
                                    </td>
                                    <td class="price" style="color: {{ $change === null ? 'var(--gray)' : ($change >= 0 ? 'var(--success)' : 'var(--error)') }};">
                                        {{ $change !== null ? ($change >= 0 ? '+' : '') . number_format($change, 2) . '%' : '—' }}
                                    </td>
                                    <td>
                                        @if($asset->price_last_updated_at)
                                            {{ $asset->price_last_updated_at->format('Y-m-d H:i') }}
                                        @else
                                            <span style="color: var(--gray);">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; margin-right: 0.25rem;">Edit</a>
                                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this asset?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card">
                    <div class="empty">
                        <p>No assets yet. <a href="{{ route('assets.create') }}">Create your first asset</a></p>
                    </div>
                </div>
            @endif
        @else
            {{-- Regular user: lean browsable grid linking into the trading terminal --}}
            @if(count($assetsWithOwned) > 0)
                <div class="watch-grid">
                    @foreach($assetsWithOwned as $item)
                        @php
                            $asset = $item['asset'];
                            $owned = $item['owned'];
                            $change = $asset->price_change_24h !== null ? (float) $asset->price_change_24h : null;
                        @endphp
                        <a href="{{ route('assets.show', $asset) }}" class="watch-card" style="text-decoration: none; color: inherit; display: block;">
                            <div class="watch-card-head">
                                <span>{{ $asset->symbol }}</span>
                                @if($item['is_watched'] ?? false)
                                    <span title="On your watchlist">★</span>
                                @endif
                            </div>
                            <div style="font-size: 0.8rem; color: var(--gray); margin-bottom: 0.5rem;">{{ $asset->name }}</div>
                            <div style="display: flex; align-items: baseline; gap: 0.6rem; flex-wrap: wrap;">
                                <div class="watch-card-price">
                                    @if($asset->price)
                                        ${{ number_format($asset->price, 2) }}
                                    @else
                                        <span style="font-size: 1rem; color: var(--gray);">No price</span>
                                    @endif
                                </div>
                                @if($change !== null)
                                    <span style="font-size: 0.8rem; font-weight: 700; color: {{ $change >= 0 ? 'var(--success)' : 'var(--error)' }};">
                                        {{ $change >= 0 ? '▲' : '▼' }} {{ number_format(abs($change), 2) }}%
                                    </span>
                                @endif
                            </div>
                            <div style="font-size: 0.7rem; color: var(--gray); margin-top: 0.15rem;">24h change</div>
                            @if($owned > 0)
                                <div style="font-size: 0.75rem; color: var(--gray); margin-top: 0.5rem;">You own {{ number_format($owned, 8) }} {{ $asset->symbol }}</div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <div class="card">
                    <div class="empty">
                        <p>No assets available yet.</p>
                    </div>
                </div>
            @endif
        @endif
    @endauth
@endsection
