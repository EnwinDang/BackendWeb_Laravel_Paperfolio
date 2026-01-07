<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'CryptoHub'))</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        :root {
            --dark-blue: #1e3a8a;
            --dark-blue-hover: #1e40af;
            --dark-blue-light: #3b82f6;
            --white: #ffffff;
            --gray-light: #f8fafc;
            --gray: #64748b;
            --gray-dark: #334155;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
        }
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--gray-light);
            color: var(--gray-dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background-color: var(--dark-blue);
            color: var(--white);
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--white);
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .nav-links a {
            color: var(--white);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: background-color 0.2s;
            font-size: 0.95rem;
        }
        .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .main-wrapper {
            display: flex;
            flex: 1;
            width: 100%;
        }
        .admin-sidebar {
            width: 250px;
            background-color: var(--dark-blue);
            color: var(--white);
            padding: 1.5rem 0;
            min-height: calc(100vh - 60px);
            position: sticky;
            top: 60px;
            flex-shrink: 0;
        }
        .admin-sidebar strong {
            display: block;
            padding: 0 1.5rem;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            color: var(--white);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 0.75rem;
        }
        .admin-sidebar a {
            display: block;
            color: var(--white);
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            transition: background-color 0.2s;
            border-left: 3px solid transparent;
        }
        .admin-sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--white);
        }
        .admin-sidebar a.active {
            background-color: rgba(255, 255, 255, 0.15);
            border-left-color: var(--white);
        }
        .container {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
        }
        .content-wrapper {
            flex: 1;
            width: 100%;
        }
        .card {
            background: var(--white);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        h1 {
            color: var(--dark-blue);
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }
        h2 {
            color: var(--dark-blue);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        h3 {
            color: var(--dark-blue);
            margin-bottom: 0.75rem;
            font-size: 1.25rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: var(--white);
            border-radius: 8px;
            overflow: hidden;
        }
        th {
            background-color: var(--dark-blue);
            color: var(--white);
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:hover {
            background-color: var(--gray-light);
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: var(--dark-blue);
            color: var(--white);
        }
        .btn-primary:hover {
            background-color: var(--dark-blue-hover);
        }
        .btn-success {
            background-color: var(--success);
            color: var(--white);
        }
        .btn-success:hover {
            background-color: #059669;
        }
        .btn-danger {
            background-color: var(--error);
            color: var(--white);
        }
        .btn-danger:hover {
            background-color: #dc2626;
        }
        .btn-secondary {
            background-color: var(--gray);
            color: var(--white);
        }
        .btn-secondary:hover {
            background-color: var(--gray-dark);
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        input[type="datetime-local"],
        textarea,
        select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: var(--dark-blue);
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--gray-dark);
        }
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        .alert-success {
            background-color: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
        }
        .alert-error {
            background-color: #fee2e2;
            border: 1px solid #ef4444;
            color: #991b1b;
        }
        .alert-warning {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
        }
        .error-message {
            color: var(--error);
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: block;
        }
        .empty {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
        }
        .price {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        .percent-buttons {
            display: flex;
            gap: 0.25rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }
        .percent-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            background-color: var(--gray-light);
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .percent-btn:hover {
            background-color: var(--dark-blue-light);
            color: var(--white);
            border-color: var(--dark-blue-light);
        }
        .trade-form {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .trade-form input {
            width: 120px;
        }
        .footer {
            background-color: var(--dark-blue);
            color: var(--white);
            padding: 2rem;
            margin-top: auto;
            text-align: center;
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
            gap: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .footer-links a {
            color: var(--white);
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .footer-links a:hover {
            opacity: 0.8;
        }
        .footer-copyright {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
        }
        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                gap: 1rem;
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
                padding: 1rem 0;
            }
            .admin-sidebar strong {
                padding: 0 1rem;
            }
            .admin-sidebar a {
                padding: 0.5rem 1rem;
            }
            .container {
                padding: 1rem;
            }
            h1 {
                font-size: 1.5rem;
            }
            table {
                font-size: 0.875rem;
            }
            th, td {
                padding: 0.5rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="header">
        <nav class="nav">
            <a href="{{ url('/') }}" class="logo">CryptoHub</a>
            <div class="nav-links">
                @auth
                    @if(auth()->user()->is_admin)
                        {{-- Minimal navigation for admins - main navigation is in sidebar --}}
                        <a href="{{ route('profile.show', auth()->user()) }}">Profile</a>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        {{-- Full navigation for regular users --}}
                        <a href="{{ route('news.index') }}">News</a>
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
                    <a href="{{ route('news.index') }}">News</a>
                    <a href="{{ route('leaderboard.index') }}">Leaderboard</a>
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </nav>
    </header>

    <div class="main-wrapper">
        @auth
            @if(auth()->user()->is_admin)
                @include('partials.admin-nav')
            @endif
        @endauth
        
        <div class="content-wrapper">
            <main class="container">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 1.5rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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
                @endauth
            </div>
            <div class="footer-copyright">
                &copy; {{ date('Y') }} CryptoHub. All rights reserved.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

