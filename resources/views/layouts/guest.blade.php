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
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }
        .auth-container {
            background: var(--card-bg);
            border: 2px solid var(--ink);
            box-shadow: var(--shadow);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
        .auth-container h1 {
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            text-align: center;
        }
        .error-input {
            border-color: var(--error) !important;
        }
        .error-message {
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: block;
        }
        .remember {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .remember input {
            width: auto;
            margin-right: 0.5rem;
            cursor: pointer;
        }
        .remember label {
            margin: 0;
            cursor: pointer;
            font-size: 0.85rem;
            text-transform: none;
            font-weight: 500;
        }
        .back-link {
            display: block;
            margin-top: 1rem;
            text-align: center;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.85rem;
        }
        .back-link:hover {
            color: var(--ink);
            text-decoration: underline;
        }
        .theme-toggle {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
        @media (max-width: 640px) {
            .auth-container {
                padding: 1.5rem;
            }
        }
        @media (min-width: 641px) and (max-width: 1024px) {
            .auth-container {
                max-width: 450px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <button type="button" class="theme-toggle" onclick="toggleTheme()" title="Toggle dark mode">
        <span id="theme-toggle-icon">&#9789;</span>
    </button>

    <div class="auth-container">
        <x-flash-messages />

        @yield('content')
    </div>

    @include('partials.theme-toggle-script')

    @stack('scripts')
</body>
</html>
