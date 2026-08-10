<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.theme-init-script')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PaperFolio - Paper Trading Platform')</title>
    @include('partials.theme-styles')
    <style>
        .wrap { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem; }

        /* Nav */
        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.5rem;
            border-bottom: 3px solid var(--ink);
            flex-wrap: wrap;
            gap: 1rem;
            background: var(--card-bg);
        }
        .nav-logo {
            font-size: 1.3rem;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: none;
            letter-spacing: 0.02em;
        }
        .nav-logo span { background: var(--accent); color: var(--on-accent); padding: 0 0.3rem; border: 2px solid var(--ink); }
        .nav-links { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
        .nav-links a {
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0.5rem 0.75rem;
        }
        .nav-links a:hover { color: var(--accent); }
        .nav-links a.active { color: var(--accent); }
        .btn-outline { background: transparent; color: var(--ink); }
        .btn-green { background: var(--accent); color: var(--on-accent); }
        .btn-pink { background: var(--accent); color: var(--on-accent); }
        .btn-lg { padding: 0.9rem 1.75rem; font-size: 0.85rem; }

        /* Content */
        .content { padding: 3rem 0; }
        .card-icon {
            width: 42px; height: 42px;
            border: 2px solid var(--ink);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
        }
        .card p { color: var(--text-gray); font-size: 0.9rem; }

        /* Hero */
        .hero {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            padding: 4rem 0;
        }
        .hero h1 { font-size: 2.75rem; line-height: 1.15; margin-bottom: 1rem; }
        .hero h1 .hl { background: var(--accent); color: var(--on-accent); padding: 0 0.3rem; display: inline-block; }
        .hero p { color: var(--text-gray); font-size: 1rem; margin-bottom: 1.75rem; max-width: 480px; }
        .hero-actions { display: flex; gap: 1rem; flex-wrap: wrap; }

        .hero-chart {
            background: var(--card-bg);
            border: 2px solid var(--ink);
            box-shadow: var(--shadow);
            padding: 1.25rem;
        }
        .hero-chart-label { font-weight: 800; text-transform: uppercase; font-size: 1.1rem; margin-bottom: 1rem; }

        /* Sections */
        .section { padding: 3.5rem 0; border-top: 3px solid var(--ink); }
        .section-head { display: flex; justify-content: space-between; align-items: baseline; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem; }
        .section-head a { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; text-decoration: none; color: var(--accent); }

        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }

        /* Post cards */
        .post-card { display: flex; flex-direction: column; gap: 0.75rem; }
        .post-head { display: flex; align-items: center; justify-content: space-between; }
        .post-time { color: var(--text-gray); font-size: 0.75rem; }
        .post-author { font-weight: 800; font-size: 0.85rem; }
        .post-content { font-size: 0.9rem; color: var(--ink); }
        .post-content .cashtag {
            font-weight: 800;
            background: var(--accent);
            color: var(--on-accent);
            padding: 0.05rem 0.35rem;
            text-decoration: none;
        }
        .post-stats { color: var(--text-gray); font-size: 0.8rem; display: flex; gap: 1rem; }

        /* CTA */
        .cta { text-align: center; padding: 4rem 1.5rem; border-top: 3px solid var(--ink); }
        .cta h2 { font-size: 2rem; margin-bottom: 1rem; }
        .cta .hl { background: var(--accent); color: var(--on-accent); padding: 0 0.3rem; }
        .cta p { color: var(--text-gray); max-width: 500px; margin: 0 auto 2rem; }

        .footer {
            border-top: 3px solid var(--ink);
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .footer-logo { font-weight: 800; text-transform: uppercase; }
        .footer-links { display: flex; gap: 1.5rem; }
        .footer-links a { text-decoration: none; font-size: 0.8rem; color: var(--text-gray); text-transform: uppercase; }
        .footer-links a:hover { color: var(--ink); }

        @media (max-width: 800px) {
            .hero { grid-template-columns: 1fr; padding: 2.5rem 0; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .hero h1 { font-size: 2rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="nav">
        <a href="{{ url('/') }}" class="nav-logo">Paper<span>Folio</span></a>
        <div class="nav-links">
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
            <a href="{{ route('news.index') }}" class="{{ request()->routeIs('news.*') ? 'active' : '' }}">Announcements</a>
            <a href="{{ route('faq.index') }}" class="{{ request()->routeIs('faq.*') ? 'active' : '' }}">FAQ</a>
            <a href="{{ route('leaderboard.index') }}" class="{{ request()->routeIs('leaderboard.*') ? 'active' : '' }}">Leaderboard</a>
            <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
            <a href="{{ route('register') }}" class="btn btn-green">Register</a>
            <button type="button" class="theme-toggle" onclick="toggleTheme()" title="Toggle dark mode">
                <span id="theme-toggle-icon">&#9789;</span>
            </button>
        </div>
    </header>

    <div class="wrap content">
        <x-flash-messages />
        @yield('content')
    </div>

    <footer class="footer">
        <div class="footer-logo">PaperFolio</div>
        <div class="footer-links">
            <a href="{{ route('faq.index') }}">FAQ</a>
            <a href="{{ route('contact.show') }}">Contact</a>
            <a href="{{ route('news.index') }}">Announcements</a>
        </div>
    </footer>

    @include('partials.theme-toggle-script')

    @stack('scripts')
</body>
</html>
