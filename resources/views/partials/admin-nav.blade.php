@auth
    @if(auth()->user()->is_admin)
        <div style="background: #f5f5f5; padding: 1rem; margin-bottom: 2rem; border-bottom: 2px solid #1b1b18;">
            <strong style="margin-right: 1rem;">Admin Panel:</strong>
            <a href="{{ route('assets.index') }}" style="margin-right: 1rem; color: #1b1b18; text-decoration: none;">Assets</a>
            <a href="{{ route('users.index') }}" style="margin-right: 1rem; color: #1b1b18; text-decoration: none;">Users</a>
            <a href="{{ route('news.index') }}" style="margin-right: 1rem; color: #1b1b18; text-decoration: none;">News</a>
            <a href="{{ route('faq.index') }}" style="margin-right: 1rem; color: #1b1b18; text-decoration: none;">FAQ</a>
        </div>
    @endif
@endauth

