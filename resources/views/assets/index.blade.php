@extends('layouts.app')

@section('title', 'Assets')

@section('content')
    <h1>Assets</h1>

    @auth
        @if(auth()->user()->is_admin)
            <div style="margin-bottom: 1rem;">
                <a href="{{ route('assets.create') }}" class="btn btn-primary">Create Asset</a>
            </div>
        @endif
    @endauth

    @if(count($assetsWithOwned) > 0)
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Symbol</th>
                        <th>Name</th>
                        @auth
                            @if(auth()->user()->is_admin)
                                <th>CoinGecko ID</th>
                            @endif
                        @endauth
                        <th>Price</th>
                        <th>Last Updated</th>
                        @auth
                            @if(!auth()->user()->is_admin)
                                <th>Owned</th>
                                <th>Actions</th>
                            @else
                                <th>Actions</th>
                            @endif
                        @endauth
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
                            @auth
                                @if(auth()->user()->is_admin)
                                    <td>
                                        @if($asset->coingecko_id)
                                            <code>{{ $asset->coingecko_id }}</code>
                                        @else
                                            <span style="color: var(--gray);">—</span>
                                        @endif
                                    </td>
                                @endif
                            @endauth
                            <td class="price">
                                @if($asset->price)
                                    ${{ number_format($asset->price, 2) }}
                                @else
                                    <span style="color: var(--gray);">—</span>
                                @endif
                            </td>
                            <td>
                                @if($asset->price_last_updated_at)
                                    {{ $asset->price_last_updated_at->format('Y-m-d H:i') }}
                                @else
                                    <span style="color: var(--gray);">—</span>
                                @endif
                            </td>
                            @auth
                                @if(auth()->user()->is_admin)
                                    <td>
                                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; margin-right: 0.25rem;">Edit</a>
                                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this asset?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Delete</button>
                                        </form>
                                    </td>
                                @elseif(!auth()->user()->is_admin)
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
                                @endif
                            @endauth
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
