<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Asset;
use App\Models\News;
use App\Models\NewsComment;
use App\Models\FaqCategory;
use App\Models\FaqItem;
use App\Models\Trade;
use App\Models\ContactSubmission;
use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@ehb.be',
            'password' => Hash::make('Password!321'),
            'is_admin' => true,
            'username' => 'admin',
        ]);

        // Create Demo Users
        $user1 = User::factory()->create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
            'username' => 'alice_trader',
            'date_of_birth' => Carbon::parse('1995-05-15'),
            'about_me' => 'Crypto enthusiast and day trader. Love analyzing market trends and making strategic trades.',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
            'username' => 'bob_crypto',
            'date_of_birth' => Carbon::parse('1992-08-22'),
            'about_me' => 'Long-term investor focused on building a diverse crypto portfolio.',
        ]);

        $user3 = User::factory()->create([
            'name' => 'Charlie Brown',
            'email' => 'charlie@example.com',
            'password' => Hash::make('password'),
            'username' => 'charlie_b',
            'date_of_birth' => Carbon::parse('1998-12-10'),
            'about_me' => 'New to crypto trading, learning the ropes!',
        ]);

        // Seed Assets
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

        $assets = [];
        foreach ($coins as $coin) {
            $assets[] = Asset::create($coin);
        }

        // Populate live prices immediately, so the app has real price/24h-change
        // data right after seeding without depending on the scheduler being active.
        // Safe to call even without internet access: the command catches its own
        // failures internally and leaves prices null rather than throwing.
        Artisan::call('assets:update-prices');

        $btc = $assets[0]; // Bitcoin
        $eth = $assets[1]; // Ethereum
        $sol = $assets[3]; // Solana
        $ada = $assets[5]; // Cardano

        // Create Demo Trades (all within $1,000 starting balance)
        // Alice's trades - Day trader, made some profit
        Trade::create([
            'user_id' => $user1->id,
            'asset_id' => $btc->id,
            'type' => 'buy',
            'amount' => 0.005,
            'price_snapshot' => 90000.00,
            'created_at' => Carbon::now()->subDays(5),
        ]); // Spent: $450

        Trade::create([
            'user_id' => $user1->id,
            'asset_id' => $btc->id,
            'type' => 'sell',
            'amount' => 0.005,
            'price_snapshot' => 92000.00,
            'created_at' => Carbon::now()->subDays(4),
        ]); // Received: $460 (profit: $10)

        Trade::create([
            'user_id' => $user1->id,
            'asset_id' => $eth->id,
            'type' => 'buy',
            'amount' => 0.15,
            'price_snapshot' => 3000.00,
            'created_at' => Carbon::now()->subDays(3),
        ]); // Spent: $450

        Trade::create([
            'user_id' => $user1->id,
            'asset_id' => $eth->id,
            'type' => 'sell',
            'amount' => 0.15,
            'price_snapshot' => 3100.00,
            'created_at' => Carbon::now()->subDays(2),
        ]); // Received: $465 (profit: $15)

        // Bob's trades - Long-term holder, made a small profit
        Trade::create([
            'user_id' => $user2->id,
            'asset_id' => $eth->id,
            'type' => 'buy',
            'amount' => 0.3,
            'price_snapshot' => 2900.00,
            'created_at' => Carbon::now()->subDays(7),
        ]); // Spent: $870

        Trade::create([
            'user_id' => $user2->id,
            'asset_id' => $eth->id,
            'type' => 'sell',
            'amount' => 0.1,
            'price_snapshot' => 2950.00,
            'created_at' => Carbon::now()->subDays(3),
        ]); // Received: $295 (profit: $5 on 0.1 ETH)

        // Charlie's trades - Beginner, made a small loss
        Trade::create([
            'user_id' => $user3->id,
            'asset_id' => $btc->id,
            'type' => 'buy',
            'amount' => 0.005,
            'price_snapshot' => 88000.00,
            'created_at' => Carbon::now()->subDays(6),
        ]); // Spent: $440

        Trade::create([
            'user_id' => $user3->id,
            'asset_id' => $btc->id,
            'type' => 'sell',
            'amount' => 0.005,
            'price_snapshot' => 87000.00,
            'created_at' => Carbon::now()->subDays(2),
        ]); // Received: $435 (loss: $5)

        Trade::create([
            'user_id' => $user3->id,
            'asset_id' => $sol->id,
            'type' => 'buy',
            'amount' => 4.0,
            'price_snapshot' => 130.00,
            'created_at' => Carbon::now()->subDays(4),
        ]); // Spent: $520

        // Create Watchlist entries
        $user1->watchedAssets()->attach([$btc->id, $eth->id, $sol->id]);
        $user2->watchedAssets()->attach([$eth->id, $ada->id]);
        $user3->watchedAssets()->attach([$btc->id]);

        // Create FAQ Categories and Items
        $gettingStarted = FaqCategory::create([
            'name' => 'Getting Started',
            'order' => 1,
        ]);

        $trading = FaqCategory::create([
            'name' => 'Trading',
            'order' => 2,
        ]);

        $account = FaqCategory::create([
            'name' => 'Account & Profile',
            'order' => 3,
        ]);

        // FAQ Items
        FaqItem::create([
            'faq_category_id' => $gettingStarted->id,
            'question' => 'What is PaperFolio?',
            'answer' => 'PaperFolio is a paper trading platform where you can practice cryptocurrency trading with virtual money. All users start with $1,000 and can buy and sell various cryptocurrencies to test their trading strategies.',
            'order' => 1,
        ]);

        FaqItem::create([
            'faq_category_id' => $gettingStarted->id,
            'question' => 'How do I get started?',
            'answer' => 'Simply register for an account, and you\'ll receive $1,000 in virtual cash to start trading. Browse the available assets, research prices, and make your first trade!',
            'order' => 2,
        ]);

        FaqItem::create([
            'faq_category_id' => $trading->id,
            'question' => 'How do I buy cryptocurrency?',
            'answer' => 'Navigate to the Assets page or your Dashboard, find the cryptocurrency you want to buy, enter the dollar amount you wish to spend, and click "Buy". The system will calculate how much of that asset you\'ll receive based on the current price.',
            'order' => 1,
        ]);

        FaqItem::create([
            'faq_category_id' => $trading->id,
            'question' => 'How do I sell cryptocurrency?',
            'answer' => 'On the Assets page or Dashboard, find an asset you own, enter the amount you want to sell (or use the percentage buttons: 25%, 50%, 75%, or 100%), and click "Sell".',
            'order' => 2,
        ]);

        FaqItem::create([
            'faq_category_id' => $trading->id,
            'question' => 'Where do prices come from?',
            'answer' => 'Prices are fetched from the CoinGecko API and updated automatically every 5 minutes when the scheduler is running. Prices reflect real-time market data.',
            'order' => 3,
        ]);

        FaqItem::create([
            'faq_category_id' => $account->id,
            'question' => 'Can I change my profile information?',
            'answer' => 'Yes! Log in and visit your profile page. Click "Edit Profile" to update your username, date of birth, profile picture, and "About me" section.',
            'order' => 1,
        ]);

        FaqItem::create([
            'faq_category_id' => $account->id,
            'question' => 'What is the watchlist feature?',
            'answer' => 'The watchlist allows you to track specific cryptocurrencies you\'re interested in. Click the star icon next to any asset to add it to your watchlist. You can view all watched assets on your dashboard.',
            'order' => 2,
        ]);

        // Create News Items with images
        // Helper function to copy images from public/images/ to storage/app/public/news/
        $copyPublicImage = function($publicImageName, $targetName) {
            $publicPath = public_path("images/{$publicImageName}");
            $targetPath = "news/{$targetName}";
            
            if (file_exists($publicPath)) {
                // Ensure news directory exists
                Storage::disk('public')->makeDirectory('news');
                // Copy file from public/images/ to storage/app/public/news/
                Storage::disk('public')->put($targetPath, file_get_contents($publicPath));
                return $targetPath;
            }
            return null;
        };

        $news1 = News::create([
            'title' => 'Leverage Trading is Now Live',
            'excerpt' => 'Go long or short on any asset at 5x, 10x, or 100x leverage. A simplified liquidation model means you can only ever lose your margin — never more.',
            'image' => $copyPublicImage('BTC-ATH.jpg', 'bitcoin-ath-seeded.jpg'),
            'content' => 'You can now open leveraged positions directly from any asset\'s trading terminal.

Pick a direction (long or short), choose 5x, 10x, or 100x leverage, and set how much of your virtual cash you want to put up as margin. Your position\'s profit or loss moves with the market price, multiplied by your leverage.

We kept the liquidation model simple on purpose: if a position\'s losses reach 100% of its margin, it gets automatically closed the next time prices refresh. You can never lose more than the margin you put into that position — your remaining cash balance is always safe.

Head to any asset\'s page and switch to the "Leverage" tab on the trade panel to try it out.',
            'publication_date' => Carbon::now()->subDays(2),
        ]);

        $news2 = News::create([
            'title' => 'Introducing the Social Feed',
            'excerpt' => 'Post your trade calls, like other traders\' posts, and use $cashtags to link straight to an asset\'s chart. Trading is more fun with company.',
            'image' => $copyPublicImage('ethereum-staking.png', 'ethereum-staking-seeded.png'),
            'content' => 'PaperFolio now has a Feed where you can share what you\'re trading and why.

Write a post and mention any tradable asset with a $cashtag, like $BTC or $SOL — it automatically becomes a clickable link that takes other traders straight to that asset\'s live chart and trade panel. Like posts you agree with, and check any asset\'s page to see every post that mentions it.

This is entirely optional — your trades stay yours to make either way — but it\'s a good way to see what the rest of the community is watching.',
            'publication_date' => Carbon::now()->subDays(5),
        ]);

        $news3 = News::create([
            'title' => 'New Asset Added: Solana (SOL)',
            'excerpt' => 'Solana is now tradable on PaperFolio, alongside Bitcoin, Ethereum, and the rest of our supported assets.',
            'image' => $copyPublicImage('-1x-1.webp', 'solana-logo-seeded.webp'),
            'content' => 'Solana (SOL) has been added to the list of assets you can trade on PaperFolio, with live pricing refreshed automatically from CoinGecko every 5 minutes.

You can buy and sell it with your virtual cash, add it to your watchlist, or open a leveraged long/short position on it, just like any other supported asset.

Have a suggestion for another asset we should add? Let us know through the contact page.',
            'publication_date' => Carbon::now()->subDays(8),
        ]);

        // Create News Comments
        NewsComment::create([
            'news_id' => $news1->id,
            'user_id' => $user1->id,
            'content' => 'Finally! Been waiting to try shorting without risking real money. The margin-only-loss thing is a nice safety net.',
            'created_at' => Carbon::now()->subDays(1),
        ]);

        NewsComment::create([
            'news_id' => $news1->id,
            'user_id' => $user2->id,
            'content' => 'Tried 100x on a whim, got liquidated in about 10 minutes. Good lesson to learn with fake money instead of real.',
            'created_at' => Carbon::now()->subHours(12),
        ]);

        NewsComment::create([
            'news_id' => $news2->id,
            'user_id' => $user1->id,
            'content' => 'Love the $cashtag links, makes it so easy to jump from a post straight into the chart.',
            'created_at' => Carbon::now()->subDays(4),
        ]);

        // Create Contact Submissions
        ContactSubmission::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Question about trading limits',
            'message' => 'Hello, I was wondering if there are any limits on how much I can trade per day? Also, can I trade multiple times in a day?',
            'read' => false,
            'created_at' => Carbon::now()->subDays(3),
        ]);

        ContactSubmission::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'subject' => 'Feature request: Price alerts',
            'message' => 'Would it be possible to add price alerts? I\'d love to be notified when a cryptocurrency reaches a certain price.',
            'read' => true,
            'admin_response' => 'Thank you for your suggestion! Price alerts are live now - head to the Price Alerts page to get notified when any asset hits your target price.',
            'responded_at' => Carbon::now()->subDays(1),
            'created_at' => Carbon::now()->subDays(5),
        ]);

        // Create Messages between users
        Message::create([
            'sender_id' => $user1->id,
            'recipient_id' => $user2->id,
            'message' => 'Hey Bob! I saw your portfolio - nice trades on Ethereum! What\'s your strategy?',
            'read_at' => Carbon::now()->subDays(2)->addMinutes(10),
            'created_at' => Carbon::now()->subDays(2),
        ]);

        Message::create([
            'sender_id' => $user2->id,
            'recipient_id' => $user1->id,
            'message' => 'Thanks Alice! I\'m focusing on long-term holds. I believe in ETH\'s potential, especially with Ethereum 2.0 coming.',
            'read_at' => Carbon::now()->subDays(2)->addMinutes(40),
            'created_at' => Carbon::now()->subDays(2)->addMinutes(30),
        ]);

        Message::create([
            'sender_id' => $user1->id,
            'recipient_id' => $user2->id,
            'message' => 'That makes sense! I\'m more of a day trader, but I respect the long-term approach. Good luck with your trades!',
            'read_at' => null,
            'created_at' => Carbon::now()->subDays(1),
        ]);

        Message::create([
            'sender_id' => $user3->id,
            'recipient_id' => $user1->id,
            'message' => 'Hi Alice! I\'m new to trading. Any tips for a beginner?',
            'read_at' => null,
            'created_at' => Carbon::now()->subHours(6),
        ]);
    }
}
