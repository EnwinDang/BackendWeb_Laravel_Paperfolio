@extends(auth()->check() ? (auth()->user()->is_admin ? 'layouts.app' : 'layouts.dashboard') : 'layouts.marketing')

@section('title', 'Leaderboard')

@push('styles')
<style>
    .lb-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    .lb-view-toggle {
        display: flex;
        gap: 0.5rem;
        flex-shrink: 0;
    }
    .lb-week-nav {
        margin-bottom: 1.5rem;
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
    }

    /* Podium */
    .podium {
        display: grid;
        grid-template-columns: 1fr 1.15fr 1fr;
        gap: 1.25rem;
        align-items: end;
        margin-bottom: 2rem;
    }
    .podium-card {
        --medal: var(--ink);
        position: relative;
        background: var(--card-bg);
        border: 2px solid var(--ink);
        border-top: 6px solid var(--medal);
        box-shadow: var(--shadow);
        padding: 1.5rem 1.25rem;
        transition: transform 0.15s, box-shadow 0.15s, border-color 0.15s;
        cursor: pointer;
    }
    .podium-card.featured {
        padding-top: 2rem;
        border-width: 3px;
        border-top-width: 8px;
    }
    .podium-card.rank-1 { --medal: #eab308; }
    .podium-card.rank-2 { --medal: #9ca3af; }
    .podium-card.rank-3 { --medal: #cd7f32; }

    .podium-card:hover {
        transform: translate(-4px, -4px);
        box-shadow: 8px 8px 0 var(--medal);
        border-color: var(--medal);
    }

    .podium-rank {
        position: absolute;
        top: -14px;
        left: -14px;
        background: var(--medal);
        color: #1a1a1a;
        font-weight: 800;
        font-size: 0.85rem;
        padding: 0.3rem 0.6rem;
        border: 2px solid var(--ink);
    }
    .podium-card.featured .podium-rank {
        font-size: 1rem;
    }
    .podium-avatar {
        width: 56px;
        height: 56px;
        border: 2px solid var(--ink);
        object-fit: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.3rem;
        background: var(--accent-dim);
        margin-bottom: 0.75rem;
        transition: border-color 0.15s;
    }
    .podium-card:hover .podium-avatar {
        border-color: var(--medal);
    }
    .podium-card.featured .podium-avatar {
        width: 72px;
        height: 72px;
        font-size: 1.6rem;
    }
    .podium-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .podium-name {
        font-weight: 800;
        font-size: 1.05rem;
        text-decoration: none;
        color: var(--ink);
        display: block;
        margin-bottom: 0.15rem;
        word-break: break-word;
    }
    .podium-card.featured .podium-name {
        font-size: 1.3rem;
    }
    .podium-divider {
        border: none;
        border-top: 2px solid var(--ink);
        margin: 0.85rem 0;
    }
    .podium-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }
    .podium-stat-label {
        color: var(--text-gray);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.7rem;
    }
    .podium-pnl {
        font-weight: 800;
        padding: 0.15rem 0.5rem;
        border: 2px solid var(--ink);
    }
    .podium-pnl.positive { background: var(--accent-dim); color: var(--success); }
    .podium-pnl.negative { background: var(--soft-red-bg); color: var(--soft-red-text); }

    @media (max-width: 700px) {
        .podium {
            grid-template-columns: 1fr;
        }
        .podium-card.featured {
            order: -1;
        }
    }
</style>
@endpush

@section('content')
    @php
        $periodNoun = match($view) { 'yearly' => 'year', 'monthly' => 'month', default => 'week' };
        $periodLabel = match($view) {
            'yearly' => $periodStart->format('Y'),
            'monthly' => $periodStart->format('F Y'),
            default => $periodStart->format('F j') . ' - ' . $periodEnd->format('F j, Y'),
        };
    @endphp
    <div class="lb-header">
        <div>
            <h1 style="margin-bottom: 0.25rem;">{{ ucfirst($periodNoun) }}ly Leaderboard</h1>
            <p style="color: var(--gray); margin: 0;">
                Top traders by realized profit percentage for <strong>{{ $periodLabel }}</strong>
            </p>
        </div>
        <div class="lb-view-toggle">
            <a href="{{ route('leaderboard.index', ['view' => 'weekly', 'week' => now()->format('Y-m-d')]) }}" class="btn {{ $view === 'weekly' ? 'btn-primary' : 'btn-secondary' }}">Weekly</a>
            <a href="{{ route('leaderboard.index', ['view' => 'monthly', 'month' => now()->format('Y-m')]) }}" class="btn {{ $view === 'monthly' ? 'btn-primary' : 'btn-secondary' }}">Monthly</a>
            <a href="{{ route('leaderboard.index', ['view' => 'yearly', 'year' => now()->year]) }}" class="btn {{ $view === 'yearly' ? 'btn-primary' : 'btn-secondary' }}">Yearly</a>
        </div>
    </div>

    @if($view === 'weekly')
        <div class="lb-week-nav">
            <a href="{{ route('leaderboard.index', ['view' => 'weekly', 'week' => $previousPeriod]) }}" class="btn btn-secondary">← Previous Week</a>
            <a href="{{ route('leaderboard.index', ['view' => 'weekly', 'week' => now()->format('Y-m-d')]) }}" class="btn btn-secondary">Current Week</a>
            @if($nextPeriod <= now()->format('Y-m-d'))
                <a href="{{ route('leaderboard.index', ['view' => 'weekly', 'week' => $nextPeriod]) }}" class="btn btn-secondary">Next Week →</a>
            @endif
        </div>
    @elseif($view === 'monthly')
        <div class="lb-week-nav">
            <a href="{{ route('leaderboard.index', ['view' => 'monthly', 'month' => $previousPeriod]) }}" class="btn btn-secondary">← Previous Month</a>
            <a href="{{ route('leaderboard.index', ['view' => 'monthly', 'month' => now()->format('Y-m')]) }}" class="btn btn-secondary">Current Month</a>
            @if($nextPeriod <= now()->format('Y-m'))
                <a href="{{ route('leaderboard.index', ['view' => 'monthly', 'month' => $nextPeriod]) }}" class="btn btn-secondary">Next Month →</a>
            @endif
        </div>
    @else
        <div class="lb-week-nav">
            <a href="{{ route('leaderboard.index', ['view' => 'yearly', 'year' => $previousPeriod]) }}" class="btn btn-secondary">← Previous Year</a>
            <a href="{{ route('leaderboard.index', ['view' => 'yearly', 'year' => now()->year]) }}" class="btn btn-secondary">Current Year</a>
            @if($nextPeriod <= now()->year)
                <a href="{{ route('leaderboard.index', ['view' => 'yearly', 'year' => $nextPeriod]) }}" class="btn btn-secondary">Next Year →</a>
            @endif
        </div>
    @endif

    @if(empty($leaderboard))
        <div class="card">
            <div class="empty">
                <p>No trades found for this {{ $periodNoun }}. Be the first to make a trade!</p>
            </div>
        </div>
    @else
        @php
            $formatVolume = function ($v) {
                if ($v >= 1000000) return '$' . number_format($v / 1000000, 2) . 'M';
                if ($v >= 1000) return '$' . number_format($v / 1000, 1) . 'K';
                return '$' . number_format($v, 2);
            };
            $top3 = collect($leaderboard)->take(3)->values();
            // Render order left-to-right: #2, #1, #3 (rank paired with each slot)
            $podiumOrder = [
                ['rank' => 2, 'entry' => $top3->get(1)],
                ['rank' => 1, 'entry' => $top3->get(0)],
                ['rank' => 3, 'entry' => $top3->get(2)],
            ];
        @endphp

        <div class="podium">
            @foreach($podiumOrder as $podiumSlot)
                @if($podiumSlot['entry'])
                    @php
                        $slot = $podiumSlot['entry'];
                        $rank = $podiumSlot['rank'];
                        $isFeatured = $rank === 1;
                    @endphp
                    <div class="podium-card rank-{{ $rank }} {{ $isFeatured ? 'featured' : '' }}">
                        <div class="podium-rank">#{{ $rank }}</div>
                        <div class="podium-avatar">
                            @if($slot['user']->getProfilePictureUrl())
                                <img src="{{ $slot['user']->getProfilePictureUrl() }}" alt="{{ $slot['user']->getDisplayName() }}">
                            @else
                                {{ strtoupper(substr($slot['user']->getDisplayName(), 0, 1)) }}
                            @endif
                        </div>
                        <a href="{{ route('profile.show', $slot['user']) }}" class="podium-name">{{ $slot['user']->getDisplayName() }}</a>
                        <hr class="podium-divider">
                        <div class="podium-stat-row">
                            <span class="podium-stat-label">Realized P/L</span>
                            <span class="podium-pnl {{ $slot['realized_profit'] >= 0 ? 'positive' : 'negative' }}">
                                {{ $slot['realized_profit'] >= 0 ? '+' : '' }}${{ number_format($slot['realized_profit'], 2) }}
                            </span>
                        </div>
                        <div class="podium-stat-row">
                            <span class="podium-stat-label">Profit %</span>
                            <strong style="color: {{ $slot['realized_profit_percent'] >= 0 ? 'var(--success)' : 'var(--error)' }};">
                                {{ $slot['realized_profit_percent'] >= 0 ? '+' : '' }}{{ number_format($slot['realized_profit_percent'], 2) }}%
                            </strong>
                        </div>
                        <div class="podium-stat-row">
                            <span class="podium-stat-label">Win Rate</span>
                            <span>{{ number_format($slot['win_rate'], 1) }}%</span>
                        </div>
                        <div class="podium-stat-row">
                            <span class="podium-stat-label">Volume</span>
                            <span>{{ $formatVolume($slot['volume']) }}</span>
                        </div>
                        <a href="{{ route('profile.show', $slot['user']) }}" class="btn {{ $isFeatured ? 'btn-primary' : 'btn-secondary' }}" style="width: 100%; text-align: center; margin-top: 0.5rem;">View Profile</a>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="card">
                <h2>Global Ladder</h2>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">Rank</th>
                            <th>Trader</th>
                            <th>Realized P/L</th>
                            <th>Win Rate</th>
                            <th>Trades</th>
                            <th>Volume</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaderboard as $index => $entry)
                            @php $rowRank = $index + 1; @endphp
                            <tr style="{{ $rowRank <= 3 ? 'background-color: var(--accent-dim);' : '' }}">
                                <td style="text-align: center; font-weight: 800;">#{{ $rowRank }}</td>
                                <td>
                                    <a href="{{ route('profile.show', $entry['user']) }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 0.6rem;">
                                        <div style="width: 32px; height: 32px; border: 2px solid var(--ink); flex-shrink: 0; overflow: hidden; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; background: var(--accent-dim);">
                                            @if($entry['user']->getProfilePictureUrl())
                                                <img src="{{ $entry['user']->getProfilePictureUrl() }}" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                {{ strtoupper(substr($entry['user']->getDisplayName(), 0, 1)) }}
                                            @endif
                                        </div>
                                        {{ $entry['user']->getDisplayName() }}
                                    </a>
                                </td>
                                <td class="price" style="color: {{ $entry['realized_profit'] >= 0 ? 'var(--success)' : 'var(--error)' }};">
                                    {{ $entry['realized_profit'] >= 0 ? '+' : '' }}${{ number_format($entry['realized_profit'], 2) }}
                                </td>
                                <td class="price">{{ number_format($entry['win_rate'], 1) }}%</td>
                                <td style="text-align: center;">{{ $entry['num_trades'] }}</td>
                                <td class="price">{{ $formatVolume($entry['volume']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>

        <div class="card">
            <p style="margin: 0; font-size: 0.875rem; color: var(--gray);">
                <strong>Note:</strong> Ranked by realized profit percentage from spot sell trades during the selected {{ $periodNoun }}.
                Win Rate is the share of sell trades closed at a profit. Volume is total $ bought + sold. Leveraged position P/L isn't included in this ranking. Only users who've made a sell trade during the {{ $periodNoun }} are shown.
            </p>
        </div>
    @endif
@endsection
