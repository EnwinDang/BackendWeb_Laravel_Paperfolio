@extends('layouts.dashboard')

@section('title', 'Price Alerts')

@section('content')
    <h1>Price Alerts</h1>

    @if($unavailable)
        <div class="card">
            <p style="color: var(--gray);">Price alerts are temporarily unavailable. Please try again later.</p>
        </div>
    @else
        <div class="card" style="margin-bottom: 1.5rem;">
            <h2>New Alert</h2>
            <form method="POST" action="{{ route('price-alerts.store') }}">
                @csrf

                <div class="form-group">
                    <label for="asset_id">Asset *</label>
                    <select id="asset_id" name="asset_id" required>
                        <option value="">Select an asset</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset['id'] }}" {{ (string) old('asset_id') === (string) $asset['id'] ? 'selected' : '' }}>
                                {{ $asset['symbol'] }} - {{ $asset['name'] }} (${{ number_format($asset['current_price'], 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="direction">Direction *</label>
                    <select id="direction" name="direction" required>
                        <option value="above" {{ old('direction') === 'above' ? 'selected' : '' }}>Above</option>
                        <option value="below" {{ old('direction') === 'below' ? 'selected' : '' }}>Below</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="target_price">Target Price (USD) *</label>
                    <input type="number" id="target_price" name="target_price" step="any" min="0" value="{{ old('target_price') }}" required>
                </div>

                <button type="submit" class="btn btn-primary">Create Alert</button>
            </form>
        </div>

        <div class="card">
            <h2>Your Alerts</h2>
            @if(count($alerts) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th>Direction</th>
                            <th>Target Price</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alerts as $alert)
                            @php
                                $asset = collect($assets)->firstWhere('id', $alert['asset_id']);
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $asset['symbol'] ?? ('#' . $alert['asset_id']) }}</strong>{{ $asset ? ' - ' . $asset['name'] : '' }}
                                </td>
                                <td>{{ ucfirst($alert['direction']) }}</td>
                                <td class="price">${{ number_format($alert['target_price'], 2) }}</td>
                                <td>
                                    @if($alert['is_triggered'])
                                        <span style="color: var(--success);">Triggered</span>
                                    @else
                                        <span style="color: var(--gray);">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('price-alerts.destroy', $alert['id']) }}" onsubmit="return confirm('Delete this alert?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color: var(--gray);">You don't have any price alerts yet. Create one above to get notified when an asset reaches your target price.</p>
            @endif
        </div>
    @endif
@endsection
