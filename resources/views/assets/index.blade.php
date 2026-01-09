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
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <strong>{{ $asset->symbol }}</strong>
                                    @auth
                                        @if(!auth()->user()->is_admin)
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
                                        @endif
                                    @endauth
                                </div>
                            </td>
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
                                            <div style="display: flex; flex-direction: column; gap: 1rem; min-width: 280px;">
                                                {{-- Buy Section --}}
                                                <div style="padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0;">
                                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                        <span style="font-size: 0.8rem; color: var(--gray); font-weight: 500;">Buy</span>
                                                        @if($cashBalance !== null)
                                                            <span style="font-size: 0.75rem; color: var(--gray);">Available: ${{ number_format($cashBalance, 2) }}</span>
                                                        @endif
                                                    </div>
                                                    <form method="POST" action="{{ route('trades.buy', $asset) }}" style="display: flex; flex-direction: column; gap: 0.5rem;">
                                                        @csrf
                                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                            <span style="color: var(--gray-dark); font-size: 0.9rem;">$</span>
                                                            <input 
                                                                type="number" 
                                                                name="dollar_amount" 
                                                                step="0.01" 
                                                                min="0.01" 
                                                                @if($cashBalance !== null) max="{{ $cashBalance }}" @endif
                                                                placeholder="0.00" 
                                                                required 
                                                                style="flex: 1; padding: 0.4rem 0.6rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.9rem;"
                                                                oninput="updateBuyEstimate({{ $asset->id }}, {{ $asset->price }}, this.value)"
                                                            >
                                                            <button type="submit" class="btn btn-success" style="padding: 0.4rem 1rem; font-size: 0.85rem; background: #10b981; border-color: #10b981;">Buy</button>
                                                        </div>
                                                        <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.7rem; color: var(--gray); margin-left: 1.25rem;">
                                                            @if($cashBalance !== null)
                                                                <span>Max: ${{ number_format($cashBalance, 2) }}</span>
                                                            @endif
                                                            <span id="buy-estimate-{{ $asset->id }}" style="display: none;">
                                                                ≈ <span id="buy-amount-{{ $asset->id }}">0</span> {{ $asset->symbol }}
                                                            </span>
                                                        </div>
                                                    </form>
                                                </div>

                                                {{-- Sell Section --}}
                                                @if($owned > 0)
                                                    <div>
                                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                            <span style="font-size: 0.8rem; color: var(--gray); font-weight: 500;">Sell</span>
                                                            <span style="font-size: 0.75rem; color: var(--gray);">Own: {{ number_format($owned, 8) }} {{ $asset->symbol }}</span>
                                                        </div>
                                                        <form method="POST" action="{{ route('trades.sell', $asset) }}" id="sell-form-{{ $asset->id }}" style="display: flex; flex-direction: column; gap: 0.5rem;">
                                                            @csrf
                                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                                <input 
                                                                    type="number" 
                                                                    name="amount" 
                                                                    step="0.00000001" 
                                                                    min="0.00000001" 
                                                                    max="{{ $owned }}" 
                                                                    placeholder="0.00000000" 
                                                                    required 
                                                                    id="sell-amount-{{ $asset->id }}"
                                                                    style="flex: 1; padding: 0.4rem 0.6rem; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.9rem;"
                                                                    oninput="updateSellEstimate({{ $asset->id }}, {{ $asset->price }}, this.value)"
                                                                >
                                                                <button type="submit" class="btn btn-danger" style="padding: 0.4rem 1rem; font-size: 0.85rem; background: #ef4444; border-color: #ef4444;">Sell</button>
                                                            </div>
                                                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                                <div class="percent-buttons" style="display: flex; gap: 0.25rem; flex: 1;">
                                                                    <button type="button" class="percent-btn" onclick="setSellAmount({{ $asset->id }}, {{ $owned }}, 0.25)" style="padding: 0.25rem 0.5rem; font-size: 0.7rem;">25%</button>
                                                                    <button type="button" class="percent-btn" onclick="setSellAmount({{ $asset->id }}, {{ $owned }}, 0.50)" style="padding: 0.25rem 0.5rem; font-size: 0.7rem;">50%</button>
                                                                    <button type="button" class="percent-btn" onclick="setSellAmount({{ $asset->id }}, {{ $owned }}, 0.75)" style="padding: 0.25rem 0.5rem; font-size: 0.7rem;">75%</button>
                                                                    <button type="button" class="percent-btn" onclick="setSellAmount({{ $asset->id }}, {{ $owned }}, 1.00)" style="padding: 0.25rem 0.5rem; font-size: 0.7rem;">100%</button>
                                                                </div>
                                                                <span id="sell-estimate-{{ $asset->id }}" style="font-size: 0.7rem; color: var(--gray); display: none;">
                                                                    ≈ $<span id="sell-value-{{ $asset->id }}">0.00</span>
                                                                </span>
                                                            </div>
                                                        </form>
                                                    </div>
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
                input.dispatchEvent(new Event('input'));
            }
        }

        function updateBuyEstimate(assetId, price, dollarAmount) {
            const estimateDiv = document.getElementById('buy-estimate-' + assetId);
            const amountSpan = document.getElementById('buy-amount-' + assetId);
            if (estimateDiv && amountSpan && dollarAmount && dollarAmount > 0) {
                const estimatedAmount = (parseFloat(dollarAmount) / price).toFixed(8);
                amountSpan.textContent = estimatedAmount;
                estimateDiv.style.display = 'block';
            } else if (estimateDiv) {
                estimateDiv.style.display = 'none';
            }
        }

        function updateSellEstimate(assetId, price, amount) {
            const estimateDiv = document.getElementById('sell-estimate-' + assetId);
            const valueSpan = document.getElementById('sell-value-' + assetId);
            if (estimateDiv && valueSpan && amount && amount > 0) {
                const estimatedValue = (parseFloat(amount) * price).toFixed(2);
                valueSpan.textContent = estimatedValue;
                estimateDiv.style.display = 'block';
            } else if (estimateDiv) {
                estimateDiv.style.display = 'none';
            }
        }
    </script>
    @endpush
@endsection
