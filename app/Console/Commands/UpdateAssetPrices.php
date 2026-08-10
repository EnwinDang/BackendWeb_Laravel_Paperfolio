<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\Position;
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
                'include_24hr_change' => 'true',
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
                    $change24h = isset($prices[$coinGeckoId]['usd_24h_change'])
                        ? (float) $prices[$coinGeckoId]['usd_24h_change']
                        : null;

                    $asset->update([
                        'price' => $price,
                        'price_change_24h' => $change24h,
                        'price_last_updated_at' => now(),
                    ]);

                    $updated++;
                    $this->info("Updated {$asset->symbol} ({$coinGeckoId}): \${$price}" . ($change24h !== null ? " ({$change24h}% 24h)" : ''));

                    $this->liquidateUnderwaterPositions($asset, $price);
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

    /**
     * Auto-close any open leveraged position on this asset whose losses have
     * wiped out its full margin. Simplified liquidation: no partial liquidation,
     * no funding rate, just a floor at -100% ROE.
     */
    private function liquidateUnderwaterPositions(Asset $asset, float $markPrice): void
    {
        $openPositions = Position::where('asset_id', $asset->id)
            ->where('status', 'open')
            ->get();

        foreach ($openPositions as $position) {
            if ($position->isLiquidatable($markPrice)) {
                $position->update([
                    'close_price' => $markPrice,
                    'realized_pnl' => -1 * (float) $position->margin_usd,
                    'status' => 'liquidated',
                    'closed_at' => now(),
                ]);

                $this->warn("Liquidated position #{$position->id} ({$position->direction} {$asset->symbol} {$position->leverage}x) for user #{$position->user_id}");
            }
        }
    }
}
