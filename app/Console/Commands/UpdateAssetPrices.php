<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateAssetPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:update-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update asset prices from CoinGecko API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating asset prices...');

        $assets = Asset::whereNotNull('coingecko_id')->get();
        
        if ($assets->isEmpty()) {
            $this->warn('No assets with CoinGecko IDs found.');
            return 0;
        }

        // Build list of CoinGecko IDs for assets we have
        $ids = [];
        $coinGeckoIdToAsset = [];
        
        foreach ($assets as $asset) {
            if (!empty($asset->coingecko_id)) {
                $ids[] = $asset->coingecko_id;
                $coinGeckoIdToAsset[$asset->coingecko_id] = $asset;
            }
        }

        if (empty($ids)) {
            $this->warn('No assets with known CoinGecko IDs found.');
            return 0;
        }

        // Fetch prices from CoinGecko API
        try {
            $idsString = implode(',', $ids);
            $response = Http::timeout(10)->get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => $idsString,
                'vs_currencies' => 'usd',
            ]);

            if (!$response->successful()) {
                throw new \Exception('API request failed with status: ' . $response->status());
            }

            $prices = $response->json();

            if (empty($prices)) {
                throw new \Exception('No price data returned from API');
            }

            $updated = 0;
            $failed = 0;

            // Update each asset with its price
            foreach ($coinGeckoIdToAsset as $coinGeckoId => $asset) {
                if (isset($prices[$coinGeckoId]['usd'])) {
                    $price = (float) $prices[$coinGeckoId]['usd'];
                    
                    $asset->update([
                        'price' => $price,
                        'price_last_updated_at' => now(),
                    ]);
                    
                    $updated++;
                    $this->info("Updated {$asset->symbol} ({$coinGeckoId}): \${$price}");
                } else {
                    $failed++;
                    $this->warn("No price data for {$asset->symbol} (CoinGecko ID: {$coinGeckoId})");
                    Log::warning("UpdateAssetPrices: No price data for {$asset->symbol} (CoinGecko ID: {$coinGeckoId})");
                }
            }

            $this->info("Price update complete. Updated: {$updated}, Failed: {$failed}");
            return 0;

        } catch (\Exception $e) {
            $this->error('Failed to update prices: ' . $e->getMessage());
            Log::error('UpdateAssetPrices failed: ' . $e->getMessage());
            return 1;
        }
    }
}
