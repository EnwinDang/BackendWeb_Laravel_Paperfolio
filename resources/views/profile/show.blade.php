<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $user->username ?? $user->name }} - Profile</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        .nav { margin-bottom: 2rem; border-bottom: 1px solid #eee; padding-bottom: 1rem; }
        .nav a { color: #666; text-decoration: none; margin-right: 1rem; }
        .nav a:hover { text-decoration: underline; }
        .profile-header { display: flex; gap: 2rem; margin-bottom: 2rem; }
        .profile-picture { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; }
        .profile-info h1 { margin: 0 0 0.5rem 0; }
        .profile-section { margin-bottom: 2rem; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #1b1b18; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ route('news.index') }}">News</a>
        <a href="{{ route('faq.index') }}">FAQ</a>
        <a href="{{ route('contact.show') }}">Contact</a>
        @auth
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        @else
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Register</a>
        @endauth
    </div>

    <div class="profile-header">
        @if($user->profile_picture)
            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile" class="profile-picture">
        @else
            <div style="width: 150px; height: 150px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center;">No Photo</div>
        @endif
        <div class="profile-info">
            <h1>{{ $user->username ?? $user->name }}</h1>
            @if($user->date_of_birth)
                <p><strong>Date of Birth:</strong> {{ $user->date_of_birth->format('F j, Y') }}</p>
            @endif
            @auth
                @if(Auth::id() === $user->id)
                    <a href="{{ route('profile.edit') }}" class="btn">Edit Profile</a>
                @endif
            @endauth
        </div>
    </div>

    @if($user->about_me)
        <div class="profile-section">
            <h2>About Me</h2>
            <p>{{ $user->about_me }}</p>
        </div>
    @endif
</body>
</html>

