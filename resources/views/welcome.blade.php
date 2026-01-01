<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CryptoHub') }} - Paper Trading Platform</title>
            <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #1b1b18;
        }
        .header {
            padding: 1.5rem 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .nav {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }
        .nav-links {
            display: flex;
            gap: 1rem;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background 0.2s;
        }
        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .container {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .hero {
            color: white;
            max-width: 800px;
        }
        .hero h1 {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            line-height: 1.6;
        }
        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
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
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .btn-primary {
            background: white;
            color: #667eea;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
        }
        .features {
            margin-top: 4rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            width: 100%;
            max-width: 1000px;
        }
        .feature {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .feature h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #667eea;
        }
        .feature p {
            color: #666;
            line-height: 1.6;
        }
        .icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            .hero p {
                font-size: 1.1rem;
            }
            .nav {
                flex-direction: column;
                gap: 1rem;
            }
        }
            </style>
    </head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">CryptoHub</div>
            <div class="nav-links">
                <a href="{{ route('news.index') }}">News</a>
                <a href="{{ route('faq.index') }}">FAQ</a>
                <a href="{{ route('contact.show') }}">Contact</a>
                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    @if(!auth()->user()->is_admin)
                        <a href="{{ route('portfolio.index') }}">Portfolio</a>
                    @endif
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('assets.index') }}">Admin</a>
                        <a href="{{ route('users.index') }}">Users</a>
                    @endif
                    <a href="{{ route('profile.show', auth()->user()) }}">Profile</a>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                @endauth
            </div>
                </nav>
        </header>

    <div class="container">
        <div class="hero">
            <h1>Paper Trading Made Simple</h1>
            <p>
                Practice crypto trading with virtual money. Learn the markets, test strategies, 
                and build your trading skills without any financial risk.
            </p>
            @guest
                <div class="cta-buttons">
                    <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
                    <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
                </div>
            @else
                <div class="cta-buttons">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                    <a href="{{ route('assets.index') }}" class="btn btn-secondary">View Assets</a>
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
    </div>
    </body>
</html>
