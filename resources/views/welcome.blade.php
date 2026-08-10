@extends('layouts.marketing')

@section('title', 'PaperFolio - Paper Trading Platform')

@section('content')
    <div class="hero">
        <div>
            <h1>Master the Markets<br>with <span class="hl">$1,000</span></h1>
            <p>
                Risk-free crypto paper trading with a social edge. Buy and sell with virtual
                cash, go long or short with up to 100x leverage, share your calls on the feed,
                and climb the weekly leaderboard.
            </p>
            <div class="hero-actions">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Get Started</a>
                <a href="{{ route('leaderboard.index') }}" class="btn btn-outline btn-lg">View Leaderboard</a>
            </div>
        </div>
        <div class="hero-chart">
            <div class="hero-chart-label">Live Market Data</div>
            <svg viewBox="0 0 400 180" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg" style="color: var(--accent);">
                <polyline points="0,140 40,120 80,150 120,90 160,110 200,60 240,80 280,40 320,55 360,20 400,35"
                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                <circle cx="400" cy="35" r="5" fill="currentColor" />
            </svg>
            <p style="color: var(--text-gray); font-size: 0.75rem; margin-top: 0.75rem;">
                Real prices via CoinGecko, refreshed automatically every 5 minutes.
            </p>
        </div>
    </div>

    <div class="section">
        <h2>Rules of the Vault</h2>
        <div class="grid-2">
            <div class="card">
                <div class="card-icon" style="color: var(--accent);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
                <h3>Start with $1,000</h3>
                <p>Everyone starts equal, with the same $1,000 in virtual cash. Reset your portfolio back to exactly $1,000 any time from your account settings — no real money, ever.</p>
            </div>
            <div class="card">
                <div class="card-icon" style="color: var(--accent-2);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l18-5v12L3 14v-3z"></path><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path></svg>
                </div>
                <h3>Social-First Trading</h3>
                <p>Post your calls with $cashtags, like other traders' posts, and jump straight from a post to that asset's chart and trade panel.</p>
            </div>
            <div class="card">
                <div class="card-icon" style="color: var(--accent-2);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"></circle><path d="M8.21 13.89 7 23l5-3 5 3-1.21-9.12"></path></svg>
                </div>
                <h3>Weekly, Monthly &amp; Yearly Leaderboards</h3>
                <p>Ranked by realized profit percentage, reset on whichever cadence you like so tenure alone can't camp the top spot.</p>
            </div>
            <div class="card">
                <div class="card-icon" style="color: var(--accent);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                </div>
                <h3>Leverage up to 100x</h3>
                <p>Go long or short on any asset at 5x, 10x, or 100x. A simplified liquidation model means you can only ever lose your margin — never more.</p>
            </div>
        </div>
    </div>

    @if($trendingPosts->isNotEmpty())
        <div class="section">
            <div class="section-head">
                <h2 style="margin-bottom: 0;">Trending on the Feed</h2>
                <a href="{{ route('login') }}">Log in to view feed &rarr;</a>
            </div>
            <div class="grid-3">
                @foreach($trendingPosts as $post)
                    <div class="card post-card">
                        <div class="post-head">
                            <span class="post-author">{{ $post->user->getDisplayName() }}</span>
                            <span class="post-time">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="post-content">{!! $post->renderedContent() !!}</div>
                        <div class="post-stats">
                            <span>&hearts; {{ $post->likers_count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="cta">
        <h2>Join <span class="hl">{{ number_format($traderCount) }}+</span> Traders Today</h2>
        <p>Stop losing real money while learning. Test your strategies risk-free in the PaperFolio arena.</p>
        <a href="{{ route('register') }}" class="btn btn-green btn-lg">Create Free Account</a>
    </div>
@endsection
