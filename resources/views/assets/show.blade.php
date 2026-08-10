@extends('layouts.dashboard')

@section('title', $asset->symbol . '/USD')

@push('styles')
<style>
    .terminal-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1.25rem;
        align-items: start;
    }
    .terminal-grid.expanded {
        grid-template-columns: 1fr;
    }
    .terminal-grid.expanded .trade-panel {
        display: none;
    }
    .chart-card {
        padding: 1rem;
    }
    .chart-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    .chart-toolbar .asset-title {
        font-size: 1.1rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .trade-tabs {
        display: flex;
        margin-bottom: 1rem;
    }
    .trade-tabs button {
        flex: 1;
        padding: 0.6rem;
        border: 2px solid var(--ink);
        background: var(--card-bg);
        color: var(--ink);
        font-family: inherit;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.75rem;
        cursor: pointer;
    }
    .trade-tabs button.active {
        background: var(--ink);
        color: var(--ink-contrast);
    }
    .trade-tabs button:first-child {
        border-right: none;
    }
    .side-toggle {
        display: flex;
        margin-bottom: 0.75rem;
    }
    .side-toggle button {
        flex: 1;
        padding: 0.6rem;
        border: 2px solid var(--ink);
        background: var(--card-bg);
        color: var(--ink);
        font-family: inherit;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.75rem;
        cursor: pointer;
    }
    .side-toggle button:first-child {
        border-right: none;
    }
    .side-toggle button[data-side="long"].active {
        background: var(--accent);
        color: var(--on-accent);
    }
    .side-toggle button[data-side="short"].active {
        background: var(--soft-red-bg);
        color: var(--soft-red-text);
    }
    .leverage-options {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .leverage-options button {
        flex: 1;
        padding: 0.5rem;
        border: 2px solid var(--ink);
        background: var(--card-bg);
        color: var(--ink);
        font-family: inherit;
        font-weight: 800;
        cursor: pointer;
    }
    .leverage-options button.active {
        background: var(--accent);
        color: var(--on-accent);
    }
    .panel-section {
        display: none;
    }
    .panel-section.active {
        display: block;
    }
</style>
@endpush

@section('content')
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('assets.index') }}" class="btn btn-secondary">&larr; All Assets</a>
        @if(auth()->check() && !auth()->user()->is_admin)
            <form method="POST" action="{{ route($isWatched ? 'assets.watchlist.remove' : 'assets.watchlist.add', $asset) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">{{ $isWatched ? '★ Watching' : '☆ Add to watchlist' }}</button>
            </form>
        @endif
    </div>

    <div class="terminal-grid" id="terminal-grid">
        <div class="card chart-card">
            <div class="chart-toolbar">
                <span class="asset-title">{{ $asset->name }} ({{ $asset->symbol }}/USD)</span>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('terminal-grid').classList.toggle('expanded')">Expand / Collapse</button>
            </div>
            <div class="tradingview-widget-container" style="width:100%">
                <div class="tradingview-widget-container__widget" style="width:100%"></div>
                <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                {
                "width": "100%",
                "height": 420,
                "symbol": "BINANCE:{{ strtoupper($asset->symbol) }}USDT",
                "interval": "D",
                "timezone": "Etc/UTC",
                "theme": "dark",
                "style": "1",
                "locale": "en",
                "allow_symbol_change": true,
                "hide_volume": true,
                "support_host": "https://www.tradingview.com"
                }
                </script>
            </div>
            <p style="color: var(--gray); font-size: 0.75rem; margin-top: 0.5rem;">
                Live chart powered by TradingView (their own market data, separate from PaperFolio's own price feed used for trading below).
            </p>
        </div>

        @if(auth()->check() && !auth()->user()->is_admin)
            <div class="card trade-panel">
                <div class="trade-tabs">
                    <button type="button" class="active" data-tab="spot" onclick="switchTab('spot')">Spot</button>
                    <button type="button" data-tab="leverage" onclick="switchTab('leverage')">Leverage</button>
                </div>

                @if(!$asset->price)
                    <p style="color: var(--gray);">No price available for this asset yet.</p>
                @else
                    {{-- SPOT --}}
                    <div class="panel-section active" data-panel="spot">
                        <h3>Buy</h3>
                        <p style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.5rem;">Available: ${{ number_format($cashBalance, 2) }}</p>
                        <form method="POST" action="{{ route('trades.buy', $asset) }}" style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.25rem;">
                            @csrf
                            <input type="number" name="dollar_amount" step="0.01" min="0.01" max="{{ $cashBalance }}" placeholder="$ Amount" required>
                            <button type="submit" class="btn btn-success">Buy {{ $asset->symbol }}</button>
                        </form>

                        @if($owned > 0)
                            <h3>Sell</h3>
                            <p style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.5rem;">Own: {{ number_format($owned, 8) }} {{ $asset->symbol }}</p>
                            <form method="POST" action="{{ route('trades.sell', $asset) }}" id="sell-form" style="display: flex; flex-direction: column; gap: 0.5rem;">
                                @csrf
                                <input type="number" name="amount" step="0.00000001" min="0.00000001" max="{{ $owned }}" placeholder="Amount" required id="sell-amount">
                                <div class="percent-buttons">
                                    <button type="button" class="percent-btn" onclick="setSellAmount(0.25)">25%</button>
                                    <button type="button" class="percent-btn" onclick="setSellAmount(0.50)">50%</button>
                                    <button type="button" class="percent-btn" onclick="setSellAmount(0.75)">75%</button>
                                    <button type="button" class="percent-btn" onclick="setSellAmount(1.00)">100%</button>
                                </div>
                                <button type="submit" class="btn btn-danger">Sell {{ $asset->symbol }}</button>
                            </form>
                        @endif
                    </div>

                    {{-- LEVERAGE --}}
                    <div class="panel-section" data-panel="leverage">
                        <form method="POST" action="{{ route('positions.open', $asset) }}">
                            @csrf
                            <div class="side-toggle">
                                <button type="button" class="active" data-side="long" onclick="setSide('long')">Long</button>
                                <button type="button" data-side="short" onclick="setSide('short')">Short</button>
                            </div>
                            <input type="hidden" name="direction" id="direction-input" value="long">

                            <p style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.4rem;">Leverage</p>
                            <div class="leverage-options">
                                <button type="button" class="active" data-lev="5" onclick="setLeverage(5)">5x</button>
                                <button type="button" data-lev="10" onclick="setLeverage(10)">10x</button>
                                <button type="button" data-lev="100" onclick="setLeverage(100)">100x</button>
                            </div>
                            <input type="hidden" name="leverage" id="leverage-input" value="5">

                            <p style="font-size: 0.75rem; color: var(--gray); margin-bottom: 0.5rem;">Available: ${{ number_format($cashBalance, 2) }}</p>
                            <input type="number" name="margin_usd" step="0.01" min="0.01" max="{{ $cashBalance }}" placeholder="Margin ($)" required style="margin-bottom: 0.75rem;">

                            <button type="submit" class="btn btn-primary" style="width: 100%;">Open position</button>
                        </form>
                        <p style="font-size: 0.7rem; color: var(--gray); margin-top: 0.75rem;">
                            Simplified model: no funding rate. If losses reach 100% of margin, the position is auto-liquidated (you lose only the margin, never more) next time prices refresh.
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    @if(auth()->check() && !auth()->user()->is_admin && $openPositions->count() > 0)
        <div class="card">
            <h2>Your Open Positions on {{ $asset->symbol }}</h2>
            <table>
                <thead>
                    <tr>
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
                            $markPrice = (float) $asset->price;
                            $pnl = $position->unrealizedPnl($markPrice);
                            $roe = $position->roePercent($markPrice);
                        @endphp
                        <tr>
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

    <div class="card">
        <h2>Posts mentioning ${{ $asset->symbol }}</h2>
        @if($posts->isEmpty())
            <div class="empty"><p>No posts mention ${{ $asset->symbol }} yet. Be the first on the <a href="{{ route('feed.index') }}">feed</a>.</p></div>
        @else
            @foreach($posts as $post)
                <div style="border-bottom: 2px solid var(--ink); padding: 0.9rem 0;">
                    <div style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.25rem;">
                        <a href="{{ route('profile.show', $post->user) }}" style="color: var(--ink); text-decoration: none;">{{ $post->user->getDisplayName() }}</a>
                        <span style="color: var(--gray); font-weight: 500;">&middot; {{ $post->created_at->diffForHumans() }}</span>
                    </div>
                    <div style="font-size: 0.9rem;">{!! $post->renderedContent() !!}</div>
                </div>
            @endforeach
        @endif
    </div>

    @push('scripts')
    <style>.cashtag { color: var(--ink); font-weight: 800; background: var(--accent-dim); padding: 0 0.2rem; text-decoration: none; }</style>
    <script>
        function switchTab(tab) {
            document.querySelectorAll('.trade-tabs button').forEach(b => b.classList.toggle('active', b.dataset.tab === tab));
            document.querySelectorAll('.panel-section').forEach(p => p.classList.toggle('active', p.dataset.panel === tab));
        }
        function setSide(side) {
            document.querySelectorAll('.side-toggle button').forEach(b => b.classList.toggle('active', b.dataset.side === side));
            document.getElementById('direction-input').value = side;
        }
        function setLeverage(lev) {
            document.querySelectorAll('.leverage-options button').forEach(b => b.classList.toggle('active', parseInt(b.dataset.lev) === lev));
            document.getElementById('leverage-input').value = lev;
        }
        function setSellAmount(percentage) {
            const owned = {{ $owned ?? 0 }};
            const input = document.getElementById('sell-amount');
            if (input) {
                input.value = (owned * percentage).toFixed(8);
            }
        }
    </script>
    @endpush
@endsection
