<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.theme-init-script')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'PaperFolio'))</title>
    @include('partials.theme-styles')
    <style>
        body {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            flex-shrink: 0;
            background-color: var(--card-bg);
            color: var(--ink);
            display: flex;
            flex-direction: column;
            padding: 1.5rem 1.1rem;
            border-right: 3px solid var(--ink);
            position: sticky;
            top: 0;
            height: 100vh;
            transition: width 0.15s;
            overflow-x: hidden;
        }
        .sidebar-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }
        .sidebar-logo {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--ink);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            min-width: 0;
        }
        .sidebar-logo .logo-full span {
            background: var(--accent);
            padding: 0 0.3rem;
            border: 2px solid var(--ink);
        }
        [data-theme="dark"] .sidebar-logo .logo-full span {
            background: var(--accent-2);
            color: #fff;
        }
        .logo-mark {
            display: none;
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            background: var(--accent);
            border: 2px solid var(--ink);
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        [data-theme="dark"] .logo-mark {
            background: var(--accent-2);
            color: #fff;
        }
        .sidebar-collapse-btn {
            background: var(--card-bg);
            border: 2px solid var(--ink);
            color: var(--ink);
            cursor: pointer;
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar-collapse-btn:hover {
            background: var(--accent);
            color: var(--on-accent);
        }
        .nav-icon {
            flex-shrink: 0;
        }

        /* Collapsed sidebar */
        [data-sidebar="collapsed"] .sidebar {
            width: 72px;
        }
        [data-sidebar="collapsed"] .sidebar-top {
            flex-direction: column;
            gap: 0.6rem;
            margin-bottom: 1.5rem;
        }
        [data-sidebar="collapsed"] .logo-full,
        [data-sidebar="collapsed"] .nav-label,
        [data-sidebar="collapsed"] .nav-text,
        [data-sidebar="collapsed"] .nav-badge,
        [data-sidebar="collapsed"] .sidebar-user-info {
            display: none;
        }
        [data-sidebar="collapsed"] .logo-mark {
            display: flex;
        }
        [data-sidebar="collapsed"] .nav-list a {
            justify-content: center;
            padding: 0.6rem;
        }
        [data-sidebar="collapsed"] .sidebar-footer {
            flex-direction: column;
            gap: 0.75rem;
        }
        [data-sidebar="collapsed"] #collapse-icon {
            transform: rotate(180deg);
        }
        .nav-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-gray);
            font-weight: 700;
            margin: 1.25rem 0 0.5rem 0.25rem;
        }
        .nav-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        .nav-list a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.6rem;
            color: var(--ink);
            text-decoration: none;
            padding: 0.55rem 0.7rem;
            border: 2px solid transparent;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.01em;
            transition: transform 0.1s;
        }
        .nav-list a:hover {
            border-color: var(--ink);
        }
        .nav-list a.active {
            background-color: var(--accent);
            border-color: var(--ink);
            box-shadow: var(--shadow-sm);
            color: var(--on-accent);
        }
        .nav-badge {
            background: var(--ink);
            color: var(--ink-contrast);
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.1rem 0.45rem;
            border: 1px solid var(--ink);
        }
        .nav-list a.active .nav-badge {
            background: var(--ink-contrast);
            color: var(--ink);
        }
        .sidebar-footer {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 2px solid var(--ink);
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .sidebar-avatar {
            width: 36px;
            height: 36px;
            background: var(--accent);
            color: var(--on-accent);
            border: 2px solid var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            flex-shrink: 0;
            overflow: hidden;
        }
        .sidebar-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .sidebar-user-name {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--ink);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-user-link {
            font-size: 0.7rem;
            color: var(--text-gray);
            text-decoration: none;
            font-weight: 700;
            text-transform: uppercase;
        }
        .sidebar-user-link:hover {
            color: var(--error);
        }

        /* Main area */
        .main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.35rem 2.25rem;
            border-bottom: 3px solid var(--ink);
            flex-wrap: wrap;
            gap: 1rem;
            background: var(--card-bg);
        }
        .topbar h1 {
            font-size: 1.6rem;
            margin-bottom: 0;
        }
        .cash-chip {
            background: var(--accent);
            border: 2px solid var(--ink);
            box-shadow: var(--shadow-sm);
            padding: 0.5rem 1.1rem;
            font-size: 0.8rem;
            font-weight: 800;
            color: var(--on-accent);
            display: flex;
            align-items: center;
            gap: 0.4rem;
            text-transform: uppercase;
        }
        .content {
            flex: 1;
            padding: 2rem 2.25rem 3rem;
        }

        @media (max-width: 900px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                flex-direction: row;
                align-items: center;
                flex-wrap: wrap;
                padding: 1rem;
                border-right: none;
                border-bottom: 3px solid var(--ink);
            }
            .sidebar-logo {
                margin-bottom: 0;
                margin-right: auto;
            }
            .nav-label {
                display: none;
            }
            .nav-list {
                flex-direction: row;
                flex-wrap: wrap;
            }
            .sidebar-footer {
                display: none;
            }
            .topbar, .content {
                padding: 1.25rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $navUser = auth()->user();
        $navUnreadMessages = $navUser ? $navUser->receivedMessages()->where('read', false)->count() : 0;
    @endphp

    <aside class="sidebar">
        <div class="sidebar-top">
            <a href="{{ route('dashboard') }}" class="sidebar-logo">
                <span class="logo-mark">P</span><span class="logo-full">Paper<span>Folio</span></span>
            </a>
            <button type="button" class="sidebar-collapse-btn" onclick="toggleSidebar()" title="Collapse sidebar">
                <svg id="collapse-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
        </div>

        <div class="nav-label">Navigation</div>
        <ul class="nav-list">
            <li><a href="{{ route('dashboard') }}" title="Dashboard" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><x-nav-icon name="dashboard" /><span class="nav-text">Dashboard</span></a></li>
            <li><a href="{{ route('portfolio.index') }}" title="Portfolio" class="{{ request()->routeIs('portfolio.*') ? 'active' : '' }}"><x-nav-icon name="portfolio" /><span class="nav-text">Portfolio</span></a></li>
            <li>
                <a href="{{ route('assets.index') }}" title="Assets" class="{{ request()->routeIs('assets.*') ? 'active' : '' }}">
                    <x-nav-icon name="assets" /><span class="nav-text">Assets</span>
                </a>
            </li>
            <li><a href="{{ route('feed.index') }}" title="Feed" class="{{ request()->routeIs('feed.*') ? 'active' : '' }}"><x-nav-icon name="feed" /><span class="nav-text">Feed</span></a></li>
            <li>
                <a href="{{ route('messages.index') }}" title="Messages" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">
                    <x-nav-icon name="messages" /><span class="nav-text">Messages</span>
                    @if($navUnreadMessages > 0)
                        <span class="nav-badge">{{ $navUnreadMessages }}</span>
                    @endif
                </a>
            </li>
            <li><a href="{{ route('price-alerts.index') }}" title="Price Alerts" class="{{ request()->routeIs('price-alerts.*') ? 'active' : '' }}"><x-nav-icon name="price-alerts" /><span class="nav-text">Price Alerts</span></a></li>
            <li><a href="{{ route('leaderboard.index') }}" title="Leaderboard" class="{{ request()->routeIs('leaderboard.*') ? 'active' : '' }}"><x-nav-icon name="leaderboard" /><span class="nav-text">Leaderboard</span></a></li>
            <li><a href="{{ route('news.index') }}" title="Announcements" class="{{ request()->routeIs('news.*') ? 'active' : '' }}"><x-nav-icon name="announcements" /><span class="nav-text">Announcements</span></a></li>
        </ul>

        <div class="nav-label">Account</div>
        <ul class="nav-list">
            <li><a href="{{ route('profile.show', $navUser) }}" title="Profile" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}"><x-nav-icon name="profile" /><span class="nav-text">Profile</span></a></li>
            <li><a href="{{ route('contact.show') }}" title="Contact" class="{{ request()->routeIs('contact.show') ? 'active' : '' }}"><x-nav-icon name="contact" /><span class="nav-text">Contact</span></a></li>
        </ul>

        <div class="sidebar-footer">
            <div class="sidebar-avatar">
                @if($navUser->getProfilePictureUrl())
                    <img src="{{ $navUser->getProfilePictureUrl() }}" alt="">
                @else
                    {{ strtoupper(substr($navUser->getDisplayName(), 0, 1)) }}
                @endif
            </div>
            <div class="sidebar-user-info" style="min-width: 0; flex: 1;">
                <div class="sidebar-user-name">{{ $navUser->getDisplayName() }}</div>
                <a href="{{ route('logout') }}" class="sidebar-user-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <h1>@yield('title', 'Dashboard')</h1>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="cash-chip">
                    ${{ number_format($navUser->getCashBalance(), 2) }} available
                </div>
                <x-notification-bell />
                <button type="button" class="theme-toggle" onclick="toggleTheme()" title="Toggle dark mode">
                    <span id="theme-toggle-icon">&#9789;</span>
                </button>
            </div>
        </div>

        <div class="content">
            <x-flash-messages />
            @yield('content')
        </div>
    </div>

    @include('partials.theme-toggle-script')
    @include('partials.confirm-modal')

    @stack('scripts')
</body>
</html>
