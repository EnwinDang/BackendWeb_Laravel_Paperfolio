@extends('layouts.app')

@section('title', 'CryptoHub - Paper Trading Platform')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: var(--white);
        padding: 4rem 2rem;
        text-align: center;
        border-radius: 8px;
        margin-bottom: 2rem;
    }
    .hero-section h1 {
        color: var(--white);
        font-size: 3rem;
        margin-bottom: 1rem;
        line-height: 1.2;
    }
    .hero-section p {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        opacity: 0.95;
        line-height: 1.6;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }
    .cta-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    .btn-hero {
        display: inline-block;
        padding: 1rem 2rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.1rem;
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        cursor: pointer;
    }
    .btn-hero:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .btn-hero-primary {
        background: var(--white);
        color: var(--dark-blue);
    }
    .btn-hero-secondary {
        background: rgba(255, 255, 255, 0.2);
        color: var(--white);
        border: 2px solid var(--white);
    }
    .features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }
    .feature {
        background: var(--white);
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .feature:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .feature h3 {
        color: var(--dark-blue);
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .feature p {
        color: var(--gray);
        line-height: 1.6;
    }
    @media (max-width: 768px) {
        .hero-section h1 {
            font-size: 2rem;
        }
        .hero-section p {
            font-size: 1.1rem;
        }
    }
</style>
@endpush

@section('content')
    <div class="hero-section">
        <h1>Paper Trading Made Simple</h1>
        <p>
            Practice crypto trading with virtual money. Learn the markets, test strategies, 
            and build your trading skills without any financial risk.
        </p>
        @guest
            <div class="cta-buttons">
                <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">Get Started</a>
                <a href="{{ route('login') }}" class="btn-hero btn-hero-secondary">Login</a>
            </div>
        @else
            <div class="cta-buttons">
                <a href="{{ route('dashboard') }}" class="btn-hero btn-hero-primary">Go to Dashboard</a>
                <a href="{{ route('assets.index') }}" class="btn-hero btn-hero-secondary">View Assets</a>
            </div>
        @endguest
    </div>

    <div class="card" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 2px solid var(--dark-blue); margin-bottom: 2rem;">
        <div style="text-align: center; padding: 2rem;">
            <h2 style="color: var(--dark-blue); margin-bottom: 1rem; font-size: 2rem;">How It Works</h2>
            <div style="max-width: 800px; margin: 0 auto;">
                <p style="font-size: 1.25rem; color: var(--gray-dark); margin-bottom: 1.5rem; line-height: 1.8;">
                    <strong style="color: var(--dark-blue);">Every new player starts with $1,000 in virtual money!</strong>
                </p>
                <p style="font-size: 1.1rem; color: var(--gray-dark); line-height: 1.8; margin-bottom: 1rem;">
                    This is a competitive trading game where you test your skills with fake money. 
                    Buy and sell cryptocurrencies, build your portfolio, and compete on the leaderboard 
                    to see who has the best trading skills!
                </p>
                <div style="display: flex; justify-content: center; gap: 3rem; margin-top: 2.5rem; flex-wrap: wrap;">
                    <div style="text-align: center; flex: 1; min-width: 150px;">
                        <div style="font-size: 2.5rem; font-weight: bold; color: var(--dark-blue); margin-bottom: 0.5rem;">$1,000</div>
                        <div style="color: var(--gray); font-size: 0.95rem; font-weight: 500;">Starting Balance</div>
                    </div>
                    <div style="text-align: center; flex: 1; min-width: 150px;">
                        <div style="font-size: 2.5rem; font-weight: bold; color: var(--dark-blue); margin-bottom: 0.5rem;">Virtual</div>
                        <div style="color: var(--gray); font-size: 0.95rem; font-weight: 500;">Risk-Free Trading</div>
                    </div>
                    <div style="text-align: center; flex: 1; min-width: 150px;">
                        <div style="font-size: 2.5rem; font-weight: bold; color: var(--dark-blue); margin-bottom: 0.5rem;">Compete</div>
                        <div style="color: var(--gray); font-size: 0.95rem; font-weight: 500;">Weekly Leaderboard</div>
                </div>
                </div>
            </div>
        </div>
        </div>

    <div class="features">
        <div class="feature">
            <h3>Real-time Prices</h3>
            <p>Track cryptocurrency prices and make informed trading decisions with up-to-date market data.</p>
        </div>
        <div class="feature">
            <h3>Portfolio Tracking</h3>
            <p>Monitor your virtual portfolio, track your trades, and analyze your performance over time.</p>
        </div>
        <div class="feature">
            <h3>Risk-Free Learning</h3>
            <p>Practice trading strategies without risking real money. Perfect for beginners and experienced traders alike.</p>
        </div>
        <div class="feature">
            <h3>Weekly Leaderboard</h3>
            <p>Compete with other traders! See who has the best realized profit percentage each week and climb the ranks.</p>
        </div>
    </div>
@endsection
