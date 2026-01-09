<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SeedAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the top 10 non-stable coins into the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $coins = [
            ['name' => 'Bitcoin', 'symbol' => 'BTC', 'coingecko_id' => 'bitcoin'],
            ['name' => 'Ethereum', 'symbol' => 'ETH', 'coingecko_id' => 'ethereum'],
            ['name' => 'Binance Coin', 'symbol' => 'BNB', 'coingecko_id' => 'binancecoin'],
            ['name' => 'Solana', 'symbol' => 'SOL', 'coingecko_id' => 'solana'],
            ['name' => 'XRP', 'symbol' => 'XRP', 'coingecko_id' => 'ripple'],
            ['name' => 'Cardano', 'symbol' => 'ADA', 'coingecko_id' => 'cardano'],
            ['name' => 'Dogecoin', 'symbol' => 'DOGE', 'coingecko_id' => 'dogecoin'],
            ['name' => 'Avalanche', 'symbol' => 'AVAX', 'coingecko_id' => 'avalanche-2'],
            ['name' => 'Chainlink', 'symbol' => 'LINK', 'coingecko_id' => 'chainlink'],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($coins as $coin) {
            $existing = Asset::where('symbol', $coin['symbol'])->first();
            if ($existing) {
                $this->info("Skipping {$coin['symbol']} - already exists");
                $skipped++;
            } else {
                $asset = Asset::create($coin);
                $this->info("Created {$coin['symbol']} - {$coin['name']}");
                $created++;
            }
        }

        $this->info("\nCompleted: {$created} assets created, {$skipped} skipped.");
        $this->info("Total assets in database: " . Asset::count());
        
        // Fetch prices for all assets
        $this->info("\nFetching prices from CoinGecko...");
        $this->call('assets:update-prices');
        
        return 0;
    }
}
