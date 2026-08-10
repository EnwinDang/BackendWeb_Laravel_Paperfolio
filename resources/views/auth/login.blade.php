@extends('layouts.guest')

@section('title', 'Login')

@section('content')
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
                    class="@error('email') error-input @enderror"
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    class="@error('password') error-input @enderror"
                >
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="remember">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>

        <div style="text-align: center; margin-top: 1rem;">
            <a href="{{ route('password.request') }}" class="back-link">Forgot your password?</a>
        </div>

        <a href="{{ route('register') }}" class="back-link">Don't have an account? Register</a>
        <a href="{{ url('/') }}" class="back-link">← Back to home</a>
@endsection
