@extends('layouts.app')

@section('title', 'Create Asset')

@section('content')
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('assets.index') }}" class="btn btn-secondary">← Back to Assets</a>
    </div>

    <h1>Create Asset</h1>

    <div class="card">
        <form method="POST" action="{{ route('assets.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Name *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required
                    placeholder="e.g., Bitcoin"
                >
            </div>

            <div class="form-group">
                <label for="symbol">Symbol *</label>
                <input 
                    type="text" 
                    id="symbol" 
                    name="symbol" 
                    value="{{ old('symbol') }}" 
                    required
                    placeholder="e.g., BTC"
                    style="text-transform: uppercase;"
                >
            </div>

            <div class="form-group">
                <label for="coingecko_id">CoinGecko ID</label>
                <div style="position: relative;">
                    <input 
                        type="text" 
                        id="coingecko_search" 
                        autocomplete="off"
                        placeholder="Type to search for a coin (e.g., bitcoin, ethereum)..."
                        style="width: 100%;"
                    >
                    <input 
                        type="hidden" 
                        id="coingecko_id" 
                        name="coingecko_id"
                        value="{{ old('coingecko_id') }}"
                    >
                    <div id="coingecko_dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--white); border: 1px solid #cbd5e1; border-radius: 6px; max-height: 300px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-top: 2px;"></div>
                </div>
                <small style="color: var(--gray); font-size: 0.875rem; display: block; margin-top: 0.25rem;">
                    Type to search for a coin. The CoinGecko ID will be automatically selected.
                    <a href="https://www.coingecko.com/en/coins" target="_blank" style="color: var(--dark-blue);">Browse all coins on CoinGecko</a>
                </small>
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Save Asset</button>
                <a href="{{ route('assets.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        const coins = @json($coins ?? []);
        const oldValue = @json(old('coingecko_id', ''));
        
        const searchInput = document.getElementById('coingecko_search');
        const hiddenInput = document.getElementById('coingecko_id');
        const dropdown = document.getElementById('coingecko_dropdown');
        
        // Set initial value if exists
        if (oldValue) {
            const selectedCoin = coins.find(c => c.id === oldValue);
            if (selectedCoin) {
                searchInput.value = `${selectedCoin.name} (${selectedCoin.symbol.toUpperCase()}) - ${selectedCoin.id}`;
            }
        }
        
        function filterCoins(query) {
            if (!query || query.length < 2) {
                dropdown.style.display = 'none';
                return;
            }
            
            const lowerQuery = query.toLowerCase();
            const filtered = coins.filter(coin => 
                coin.name.toLowerCase().includes(lowerQuery) ||
                coin.symbol.toLowerCase().includes(lowerQuery) ||
                coin.id.toLowerCase().includes(lowerQuery)
            ).slice(0, 20); // Limit to 20 results for performance
            
            if (filtered.length === 0) {
                dropdown.innerHTML = '<div style="padding: 1rem; color: var(--gray);">No coins found</div>';
                dropdown.style.display = 'block';
                return;
            }
            
            dropdown.innerHTML = filtered.map(coin => `
                <div 
                    class="coin-option" 
                    data-id="${coin.id}"
                    style="padding: 0.75rem; cursor: pointer; border-bottom: 1px solid #e2e8f0; transition: background-color 0.2s;"
                    onmouseover="this.style.backgroundColor='var(--gray-light)'"
                    onmouseout="this.style.backgroundColor='transparent'"
                >
                    <strong>${coin.name}</strong> (${coin.symbol.toUpperCase()})<br>
                    <small style="color: var(--gray);">${coin.id}</small>
                </div>
            `).join('');
            
            dropdown.style.display = 'block';
            
            // Add click handlers
            dropdown.querySelectorAll('.coin-option').forEach(option => {
                option.addEventListener('click', function() {
                    const coinId = this.getAttribute('data-id');
                    const coin = coins.find(c => c.id === coinId);
                    if (coin) {
                        searchInput.value = `${coin.name} (${coin.symbol.toUpperCase()}) - ${coin.id}`;
                        hiddenInput.value = coinId;
                        dropdown.style.display = 'none';
                    }
                });
            });
        }
        
        searchInput.addEventListener('input', function(e) {
            filterCoins(e.target.value);
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const container = searchInput.closest('.form-group');
            if (container && !container.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
        
        // Clear hidden input if search is cleared
        searchInput.addEventListener('input', function(e) {
            if (!e.target.value) {
                hiddenInput.value = '';
            }
        });
    </script>
    @endpush
@endsection
