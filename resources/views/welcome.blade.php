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
    </div>
@endsection
