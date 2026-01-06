@extends('layouts.app')

@section('title', 'Login')

@push('styles')
<style>
    body {
        display: flex !important;
        flex-direction: column !important;
        min-height: 100vh;
        margin: 0;
        padding: 0;
    }
    .header {
        display: none !important;
    }
    .admin-nav {
        display: none !important;
    }
    .main-wrapper {
        flex: 1;
        display: flex !important;
        flex-direction: column !important;
        width: 100%;
        max-width: 100%;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 0px);
    }
    .content-wrapper {
        flex: 1;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 1rem;
        width: 100%;
        max-width: 100%;
    }
    .container {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
    }
    .auth-container {
        background: var(--white);
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 400px;
    }
    .auth-container h1 {
        margin-bottom: 1.5rem;
        font-size: 1.75rem;
        text-align: center;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--gray-dark);
    }
    .form-group input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-group input:focus {
        outline: none;
        border-color: var(--dark-blue);
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
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
        font-size: 0.95rem;
    }
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s, transform 0.1s;
    }
    .btn-primary {
        background-color: var(--dark-blue);
        color: var(--white);
    }
    .btn-primary:hover {
        background-color: var(--dark-blue-hover);
    }
    .btn-primary:active {
        transform: scale(0.98);
    }
    .back-link {
        display: block;
        margin-top: 1rem;
        text-align: center;
        color: var(--gray);
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.2s;
    }
    .back-link:hover {
        color: var(--dark-blue);
        text-decoration: underline;
    }
    .footer {
        width: 100%;
        position: relative;
        margin-top: 0;
        flex-shrink: 0;
    }
    
    /* Responsive styles */
    @media (max-width: 640px) {
        .content-wrapper {
            padding: 0.75rem;
        }
        .auth-container {
            padding: 1.5rem;
            border-radius: 0;
            box-shadow: none;
        }
        .auth-container h1 {
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group input {
            padding: 0.625rem;
            font-size: 16px; /* Prevents zoom on iOS */
        }
        .btn {
            padding: 0.625rem 1.25rem;
            font-size: 0.95rem;
        }
        .back-link {
            font-size: 0.875rem;
            margin-top: 0.75rem;
        }
    }
    
    @media (max-width: 480px) {
        .auth-container {
            padding: 1.25rem;
        }
        .auth-container h1 {
            font-size: 1.375rem;
        }
    }
    
    @media (min-width: 641px) and (max-width: 1024px) {
        .auth-container {
            max-width: 450px;
        }
    }
</style>
@endpush

@section('content')
    <div class="auth-container">
        <h1>Login</h1>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                >
            </div>

            <div class="remember">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>

        <a href="{{ route('register') }}" class="back-link">Don't have an account? Register</a>
        <a href="{{ url('/') }}" class="back-link">← Back to home</a>
    </div>
@endsection
