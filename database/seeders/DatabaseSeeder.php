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
            'question' => 'What is CryptoHub?',
            'answer' => 'CryptoHub is a paper trading platform where you can practice cryptocurrency trading with virtual money. All users start with $1,000 and can buy and sell various cryptocurrencies to test their trading strategies.',
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
            'title' => 'Bitcoin Reaches New All-Time High',
            'excerpt' => 'Bitcoin has surged past $90,000, marking a significant milestone in the cryptocurrency market. Analysts predict continued growth as institutional adoption increases.',
            'image' => $copyPublicImage('BTC-ATH.jpg', 'bitcoin-ath-seeded.jpg'),
            'content' => 'Bitcoin has reached a new all-time high, breaking through the $90,000 barrier for the first time in its history. This milestone comes as institutional investors continue to show strong interest in the leading cryptocurrency.

The price surge has been attributed to several factors, including increased adoption by major corporations, favorable regulatory developments, and growing acceptance of Bitcoin as a store of value.

Market analysts are optimistic about the future, with many predicting that Bitcoin could reach even higher levels in the coming months. However, they also caution investors to be aware of the inherent volatility in cryptocurrency markets.

"Bitcoin\'s recent performance demonstrates the growing maturity of the cryptocurrency market," said one industry expert. "While we\'re seeing strong momentum, it\'s important for investors to do their own research and invest responsibly."

The cryptocurrency market as a whole has been experiencing significant growth, with many altcoins also seeing substantial gains. Ethereum, the second-largest cryptocurrency, has also been performing well, reaching new highs of its own.

As the market continues to evolve, experts recommend that investors stay informed about market trends and regulatory developments that could impact cryptocurrency prices.',
            'publication_date' => Carbon::now()->subDays(2),
        ]);

        $news2 = News::create([
            'title' => 'Ethereum 2.0 Staking Reaches New Milestone',
            'excerpt' => 'Over 30 million ETH are now staked in Ethereum 2.0, representing a major step forward for the network\'s transition to proof-of-stake consensus.',
            'image' => $copyPublicImage('ethereum-staking.png', 'ethereum-staking-seeded.png'),
            'content' => 'Ethereum 2.0 has reached a significant milestone with over 30 million ETH now staked in the network. This represents approximately 25% of the total Ethereum supply and demonstrates strong community support for the network\'s transition to a proof-of-stake consensus mechanism.

The transition to Ethereum 2.0 is one of the most anticipated upgrades in the cryptocurrency space. It promises to improve the network\'s scalability, security, and energy efficiency.

Staking allows ETH holders to earn rewards by locking their tokens to help secure the network. The current annual percentage yield (APY) for staking is approximately 4-5%, making it an attractive option for long-term holders.

"The growing amount of staked ETH shows that the community is confident in Ethereum\'s future," said a blockchain researcher. "This is a positive sign for the network\'s long-term sustainability."

The Ethereum 2.0 upgrade is being rolled out in phases, with the final phase expected to be completed in the coming years. Once fully implemented, the network should be able to process significantly more transactions per second while using less energy.',
            'publication_date' => Carbon::now()->subDays(5),
        ]);

        $news3 = News::create([
            'title' => 'Solana Network Sees Record Transaction Volume',
            'excerpt' => 'Solana has processed over 50 billion transactions, showcasing its high-performance blockchain capabilities and growing ecosystem.',
            'image' => $copyPublicImage('-1x-1.webp', 'solana-logo-seeded.webp'),
            'content' => 'The Solana blockchain has achieved a new record, processing over 50 billion transactions since its launch. This milestone highlights the network\'s ability to handle high transaction volumes at low costs.

Solana has gained significant attention in the cryptocurrency space due to its high throughput and low transaction fees. The network can process thousands of transactions per second, making it attractive for decentralized applications (dApps) and DeFi protocols.

"The Solana network\'s performance demonstrates the potential of next-generation blockchain technology," commented a DeFi analyst. "Its ability to scale while maintaining low costs is a significant advantage."

The Solana ecosystem has been growing rapidly, with numerous projects launching on the network. These include decentralized exchanges, NFT marketplaces, gaming platforms, and various DeFi applications.

Despite its success, Solana has faced some challenges, including network outages. However, the development team has been working to improve the network\'s stability and reliability.

As the cryptocurrency market continues to evolve, Solana remains one of the most promising blockchain networks, with many experts predicting continued growth in its ecosystem and adoption.',
            'publication_date' => Carbon::now()->subDays(8),
        ]);

        // Create News Comments
        NewsComment::create([
            'news_id' => $news1->id,
            'user_id' => $user1->id,
            'content' => 'This is great news! I\'ve been holding Bitcoin for a while now, and it\'s exciting to see it reach new heights.',
            'created_at' => Carbon::now()->subDays(1),
        ]);

        NewsComment::create([
            'news_id' => $news1->id,
            'user_id' => $user2->id,
            'content' => 'I\'m curious to see how this will affect the broader market. Altcoins might follow suit!',
            'created_at' => Carbon::now()->subHours(12),
        ]);

        NewsComment::create([
            'news_id' => $news2->id,
            'user_id' => $user1->id,
            'content' => 'I\'ve been staking my ETH for months now. The rewards are decent, and I\'m supporting the network!',
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
            'admin_response' => 'Thank you for your suggestion! We\'re always looking to improve the platform. Price alerts are on our roadmap for future updates.',
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
