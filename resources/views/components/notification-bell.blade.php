@auth
    @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
    <a href="{{ route('notifications.index') }}" class="theme-toggle" style="position: relative; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;" title="Notifications">
        <span style="font-size: 1rem; line-height: 1;">&#128276;</span>
        @if($unreadCount > 0)
            <span style="position: absolute; top: -6px; right: -6px; background: var(--error); color: #fff; border-radius: 50%; min-width: 16px; height: 16px; padding: 0 3px; font-size: 0.6rem; display: flex; align-items: center; justify-content: center; font-weight: 800; border: 1px solid var(--card-bg);">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </a>
@endauth
