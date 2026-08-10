<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.theme-init-script')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'PaperFolio'))</title>
    @include('partials.theme-styles')
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background-color: var(--ink);
            color: var(--ink-contrast);
            padding: 1.1rem 0;
            border-bottom: 3px solid var(--ink);
        }
        .nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .logo {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--ink-contrast);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex-wrap: wrap;
        }
        .nav-links a {
            color: var(--ink-contrast);
            text-decoration: none;
            padding: 0.5rem 0.85rem;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            border: 2px solid transparent;
        }
        .nav-links a:hover {
            border-color: var(--ink-contrast);
        }
        .main-wrapper {
            display: flex;
            flex: 1;
            width: 100%;
        }
        .admin-sidebar {
            width: 240px;
            flex-shrink: 0;
            background-color: var(--card-bg);
            border-right: 3px solid var(--ink);
            padding: 1.5rem 1.1rem;
            min-height: 100%;
            position: sticky;
            top: 0;
        }
        .admin-sidebar strong {
            display: block;
            margin-bottom: 1rem;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-gray);
            font-weight: 700;
        }
        .admin-sidebar a {
            display: block;
            color: var(--ink);
            text-decoration: none;
            padding: 0.55rem 0.7rem;
            margin-bottom: 0.35rem;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            border: 2px solid transparent;
        }
        .admin-sidebar a:hover {
            border-color: var(--ink);
        }
        .admin-sidebar a.active {
            background-color: var(--accent);
            color: var(--on-accent);
            border-color: var(--ink);
            box-shadow: var(--shadow-sm);
        }
        .content-wrapper {
            flex: 1;
            width: 100%;
        }
        .container {
            flex: 1;
            max-width: 1100px;
            margin: 0 auto;
            padding: 2.25rem 2rem;
            width: 100%;
        }
        .footer {
            background-color: var(--ink);
            color: var(--ink-contrast);
            padding: 1.5rem;
            margin-top: auto;
            text-align: center;
            border-top: 3px solid var(--ink);
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .footer-links {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .footer-links a {
            color: var(--ink-contrast);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .footer-links a:hover {
            opacity: 0.8;
        }
        .footer-copyright {
            font-size: 0.75rem;
            opacity: 0.85;
        }
        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                gap: 0.75rem;
            }
            .nav-links {
                justify-content: center;
            }
            .main-wrapper {
                flex-direction: column;
            }
            .admin-sidebar {
                width: 100%;
                min-height: auto;
                position: relative;
                top: 0;
            }
            .container {
                padding: 1.25rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a href="{{ url('/') }}" class="logo">PaperFolio</a>
            <div class="nav-links">
                @auth
                    @if(auth()->user()->is_admin)
                        {{-- Minimal navigation for admins - main navigation is in sidebar --}}
                        <a href="{{ route('assets.index') }}">Home</a>
                        <a href="{{ route('profile.show', auth()->user()) }}">Profile</a>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        {{-- Full navigation for regular users --}}
                        <a href="{{ route('news.index') }}">Announcements</a>
                        <a href="{{ route('leaderboard.index') }}">Leaderboard</a>
                        <a href="{{ route('messages.index') }}">Messages</a>
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                        <a href="{{ route('portfolio.index') }}">Portfolio</a>
                        <a href="{{ route('profile.show', auth()->user()) }}">Profile</a>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endif
                @else
                    {{-- Navigation for guests --}}
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ route('news.index') }}">Announcements</a>
                    <a href="{{ route('leaderboard.index') }}">Leaderboard</a>
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                @endauth
                <button type="button" class="theme-toggle" onclick="toggleTheme()" title="Toggle dark mode">
                    <span id="theme-toggle-icon">&#9789;</span>
                </button>
            </div>
        </nav>
    </header>

    <div class="main-wrapper">
        <x-admin-nav />

        <div class="content-wrapper">
            <main class="container">
                <x-flash-messages />

                @yield('content')
            </main>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="{{ route('faq.index') }}">FAQ</a>
                @auth
                    @if(!auth()->user()->is_admin)
                        <a href="{{ route('contact.show') }}">Contact</a>
                    @endif
                @else
                    <a href="{{ route('contact.show') }}">Contact</a>
                @endauth
            </div>
            <div class="footer-copyright">
                &copy; {{ date('Y') }} PaperFolio. All rights reserved.
            </div>
        </div>
    </footer>

    @include('partials.theme-toggle-script')

    @stack('scripts')
</body>
</html>
