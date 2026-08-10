@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
        <h1>Reset Password</h1>

        <p style="color: var(--gray); margin-bottom: 1.5rem; text-align: center;">
            Enter your email address and we'll send you a link to reset your password.
        </p>

        <form method="POST" action="{{ route('password.email') }}">
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

            <button type="submit" class="btn btn-primary" style="width: 100%;">Send Password Reset Link</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">← Back to Login</a>
@endsection
