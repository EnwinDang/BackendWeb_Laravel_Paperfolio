@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
        <h1>Reset Password</h1>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ $email ?? old('email') }}" 
                    required 
                    autofocus
                    readonly
                    class="@error('email') error-input @enderror"
                    style="background-color: var(--gray-light);"
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
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

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Reset Password</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">← Back to Login</a>
@endsection
