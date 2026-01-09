@extends('layouts.app')

@section('title', 'Leaderboard')

@section('content')
    <h1>Weekly Leaderboard</h1>
    <p style="color: var(--gray); margin-bottom: 1.5rem;">
        Top traders based on realized profit percentage for the week of 
        <strong>{{ $weekStart->format('F j') }}</strong> - 
        <strong>{{ $weekEnd->format('F j, Y') }}</strong>
    </p>

    <div style="margin-bottom: 1.5rem; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('leaderboard.index', ['week' => $previousWeek]) }}" class="btn btn-secondary">← Previous Week</a>
        <a href="{{ route('leaderboard.index', ['week' => $currentWeek]) }}" class="btn btn-secondary">Current Week</a>
        @php
            $today = \Carbon\Carbon::now()->format('Y-m-d');
        @endphp
        @if($nextWeek <= $today)
            <a href="{{ route('leaderboard.index', ['week' => $nextWeek]) }}" class="btn btn-secondary">Next Week →</a>
        @endif
    </div>

    @if(empty($leaderboard))
        <div class="card">
            <div class="empty">
                <p>No trades found for this week. Be the first to make a trade!</p>
            </div>
        </div>
    @else
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">Rank</th>
                        <th>Trader</th>
                        <th>Realized Profit</th>
                        <th>Cost Basis</th>
                        <th>Profit %</th>
                        <th>Trades</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaderboard as $index => $entry)
                        @php
                            $rank = $index + 1;
                            $isTopThree = $rank <= 3;
                            $rowStyle = '';
                            if ($rank === 1) {
                                $rowStyle = 'background: linear-gradient(90deg, #ffd700 0%, rgba(255, 215, 0, 0.1) 100%); font-weight: bold;';
                            } elseif ($rank === 2) {
                                $rowStyle = 'background: linear-gradient(90deg, #c0c0c0 0%, rgba(192, 192, 192, 0.1) 100%); font-weight: bold;';
                            } elseif ($rank === 3) {
                                $rowStyle = 'background: linear-gradient(90deg, #cd7f32 0%, rgba(205, 127, 50, 0.1) 100%); font-weight: bold;';
                            }
                        @endphp
                        <tr style="{{ $rowStyle }}">
                            <td style="text-align: center; font-size: 1.25rem; font-weight: bold;">
                                @if($rank === 1)
                                    <span style="color: #ffd700;">#1</span>
                                @elseif($rank === 2)
                                    <span style="color: #c0c0c0;">#2</span>
                                @elseif($rank === 3)
                                    <span style="color: #cd7f32;">#3</span>
                                @else
                                    #{{ $rank }}
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('profile.show', $entry['user']) }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 0.75rem;">
                                    @if($entry['user']->getProfilePictureUrl())
                                        <img src="{{ $entry['user']->getProfilePictureUrl() }}" alt="{{ $entry['user']->getDisplayName() }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                    @else
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--dark-blue); color: var(--white); display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                            {{ strtoupper(substr($entry['user']->getDisplayName(), 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight: {{ $isTopThree ? 'bold' : 'normal' }};">{{ $entry['user']->getDisplayName() }}</div>
                                        @if($entry['user']->username)
                                            <div style="font-size: 0.875rem; color: var(--gray);">{{ $entry['user']->username }}</div>
                                        @endif
                                    </div>
                                </a>
                            </td>
                            <td class="price" style="color: {{ $entry['realized_profit'] >= 0 ? 'var(--success)' : 'var(--error)' }}; font-weight: {{ $isTopThree ? 'bold' : 'normal' }};">
                                {{ $entry['realized_profit'] >= 0 ? '+' : '' }}${{ number_format($entry['realized_profit'], 2) }}
                            </td>
                            <td class="price">
                                ${{ number_format($entry['cost_basis'], 2) }}
                            </td>
                            <td class="price" style="color: {{ $entry['realized_profit_percent'] >= 0 ? 'var(--success)' : 'var(--error)' }}; font-weight: {{ $isTopThree ? 'bold' : 'normal' }}; font-size: 1.1rem;">
                                {{ $entry['realized_profit_percent'] >= 0 ? '+' : '' }}{{ number_format($entry['realized_profit_percent'], 2) }}%
                            </td>
                            <td style="text-align: center;">
                                {{ $entry['num_trades'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card" style="background: var(--gray-light); padding: 1rem;">
            <p style="margin: 0; font-size: 0.875rem; color: var(--gray);">
                <strong>Note:</strong> Leaderboard is based on realized profit percentage from closed positions (sell trades) during the selected week.
                 <br>
                Only users who made sell trades during the week are shown.
            </p>
        </div>
    @endif
@endsection

