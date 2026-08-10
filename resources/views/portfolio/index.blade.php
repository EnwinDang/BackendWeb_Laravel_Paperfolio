@extends('layouts.dashboard')

@section('title', 'Portfolio')

@section('content')
    @if(count($portfolio) > 0 || $openPositions->count() > 0 || $closedPositions->count() > 0)
        <div class="card" style="margin-bottom: 1.5rem;">
            <h2>Profit & Loss Summary</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <div style="padding: 1rem; background: var(--gray-light); border-radius: 6px;">
                    <div style="color: var(--gray); font-size: 0.875rem; margin-bottom: 0.25rem;">Total Invested</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: var(--dark-blue);">${{ number_format($totalInvested, 2) }}</div>
                </div>
                <div style="padding: 1rem; background: var(--gray-light); border-radius: 6px;">
                    <div style="color: var(--gray); font-size: 0.875rem; margin-bottom: 0.25rem;">Current Value</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: var(--dark-blue);">${{ number_format($totalPortfolioValue, 2) }}</div>
                </div>
                <div style="padding: 1rem; background: var(--gray-light); border-radius: 6px;">
                    <div style="color: var(--gray); font-size: 0.875rem; margin-bottom: 0.25rem;">Unrealized Profit</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: {{ $totalUnrealizedProfit >= 0 ? 'var(--success)' : 'var(--error)' }};">
                        {{ $totalUnrealizedProfit >= 0 ? '+' : '' }}${{ number_format($totalUnrealizedProfit, 2) }}
                    </div>
                </div>
                <div style="padding: 1rem; background: var(--gray-light); border-radius: 6px;">
                    <div style="color: var(--gray); font-size: 0.875rem; margin-bottom: 0.25rem;">Realized Profit</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: {{ $totalRealizedProfit >= 0 ? 'var(--success)' : 'var(--error)' }};">
                        {{ $totalRealizedProfit >= 0 ? '+' : '' }}${{ number_format($totalRealizedProfit, 2) }}
                    </div>
                </div>
                <div style="padding: 1rem; background: {{ $totalProfit >= 0 ? '#d1fae5' : '#fee2e2' }}; border-radius: 6px; border: 2px solid {{ $totalProfit >= 0 ? 'var(--success)' : 'var(--error)' }};">
                    <div style="color: var(--gray); font-size: 0.875rem; margin-bottom: 0.25rem;">Total Profit/Loss</div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: {{ $totalProfit >= 0 ? 'var(--success)' : 'var(--error)' }};">
                        {{ $totalProfit >= 0 ? '+' : '' }}${{ number_format($totalProfit, 2) }}
                    </div>
                    <div style="font-size: 0.875rem; color: var(--gray); margin-top: 0.25rem;">
                        ({{ $totalProfit >= 0 ? '+' : '' }}{{ number_format($totalProfitPercent, 2) }}%)
                    </div>
                </div>
            </div>
        </div>

        @if(count($portfolio) > 0)
        <div class="card">
            <h2>Holdings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Asset</th>
                        <th>Holdings</th>
                        <th>Avg. Buy Price</th>
                        <th>Current Price</th>
                        <th>Current Value</th>
                        <th>Cost Basis</th>
                        <th>Unrealized P/L</th>
                        <th>Realized P/L</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($portfolio as $item)
                        <tr>
                            <td><strong>{{ $item['asset']->symbol }}</strong> - {{ $item['asset']->name }}</td>
                            <td class="price">{{ number_format($item['holdings'], 8) }}</td>
                            <td class="price">
                                @if($item['average_purchase_price'] > 0)
                                    ${{ number_format($item['average_purchase_price'], 2) }}
                                @else
                                    <span style="color: var(--gray);">—</span>
                                @endif
                            </td>
                            <td class="price">
                                @if($item['current_price'])
                                    ${{ number_format($item['current_price'], 2) }}
                                @else
                                    <span style="color: var(--gray);">—</span>
                                @endif
                            </td>
                            <td class="price">
                                @if($item['current_value'] !== null)
                                    ${{ number_format($item['current_value'], 2) }}
                                @else
                                    <span style="color: var(--gray);">—</span>
                                @endif
                            </td>
                            <td class="price">
                                @if($item['cost_basis'] > 0)
                                    ${{ number_format($item['cost_basis'], 2) }}
                                @else
                                    <span style="color: var(--gray);">—</span>
                                @endif
                            </td>
                            <td class="price" style="color: {{ $item['unrealized_profit'] >= 0 ? 'var(--success)' : 'var(--error)' }};">
                                @if($item['holdings'] > 0)
                                    {{ $item['unrealized_profit'] >= 0 ? '+' : '' }}${{ number_format($item['unrealized_profit'], 2) }}
                                    <br>
                                    <small style="font-size: 0.75rem;">
                                        ({{ $item['unrealized_profit'] >= 0 ? '+' : '' }}{{ number_format($item['unrealized_profit_percent'], 2) }}%)
                                    </small>
                                @else
                                    <span style="color: var(--gray);">—</span>
                                @endif
                            </td>
                            <td class="price" style="color: {{ $item['realized_profit'] >= 0 ? 'var(--success)' : 'var(--error)' }};">
                                {{ $item['realized_profit'] >= 0 ? '+' : '' }}${{ number_format($item['realized_profit'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold; background-color: var(--gray-light); border-top: 2px solid var(--dark-blue);">
                        <td colspan="4"><strong>Total</strong></td>
                        <td class="price"><strong>${{ number_format($spotPortfolioValue, 2) }}</strong></td>
                        <td class="price"><strong>${{ number_format($totalInvestedCurrent ?? 0, 2) }}</strong></td>
                        <td class="price" style="color: {{ $spotUnrealizedProfit >= 0 ? 'var(--success)' : 'var(--error)' }};">
                            <strong>{{ $spotUnrealizedProfit >= 0 ? '+' : '' }}${{ number_format($spotUnrealizedProfit, 2) }}</strong>
                        </td>
                        <td class="price" style="color: {{ $spotRealizedProfit >= 0 ? 'var(--success)' : 'var(--error)' }};">
                            <strong>{{ $spotRealizedProfit >= 0 ? '+' : '' }}${{ number_format($spotRealizedProfit, 2) }}</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        @if($openPositions->count() > 0)
        <div class="card">
            <h2>Leveraged Positions</h2>
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

        @if($closedPositions->count() > 0)
        <div class="card">
            <h2>Position History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Market</th>
                        <th>Side</th>
                        <th>Leverage</th>
                        <th>Margin</th>
                        <th>Entry Price</th>
                        <th>Close Price</th>
                        <th>Result</th>
                        <th>Closed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($closedPositions as $position)
                        <tr>
                            <td><a href="{{ route('assets.show', $position->asset) }}" style="color: inherit;"><strong>{{ $position->asset->symbol }}/USD</strong></a></td>
                            <td><span class="pill {{ $position->direction === 'long' ? 'pill-long' : 'pill-short' }}">{{ ucfirst($position->direction) }}</span></td>
                            <td>{{ $position->leverage }}x</td>
                            <td class="price">${{ number_format($position->margin_usd, 2) }}</td>
                            <td class="price">${{ number_format($position->entry_price, 2) }}</td>
                            <td class="price">${{ $position->close_price !== null ? '$' . number_format($position->close_price, 2) : '—' }}</td>
                            <td class="price" style="color: {{ $position->realized_pnl >= 0 ? 'var(--success)' : 'var(--error)' }};">
                                {{ $position->realized_pnl >= 0 ? '+' : '' }}${{ number_format($position->realized_pnl, 2) }}
                                @if($position->status === 'liquidated')
                                    <br><small style="font-size: 0.75rem; color: var(--error);">Liquidated</small>
                                @endif
                            </td>
                            <td>{{ $position->closed_at?->format('M j, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    @else
        <div class="card">
            <div class="empty">
                <p>Your portfolio is empty. Start trading to build your portfolio!</p>
            </div>
        </div>
    @endif
@endsection
