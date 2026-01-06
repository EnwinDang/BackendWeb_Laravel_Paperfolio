@auth
    @if(auth()->user()->is_admin)
        <aside class="admin-sidebar">
            <strong>Admin Panel</strong>
            <a href="{{ route('assets.index') }}" class="{{ request()->routeIs('assets.*') ? 'active' : '' }}">Assets</a>
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Users</a>
            <a href="{{ route('news.index') }}" class="{{ request()->routeIs('news.*') ? 'active' : '' }}">News</a>
            <a href="{{ route('faq.index') }}" class="{{ request()->routeIs('faq.*') ? 'active' : '' }}">FAQ</a>
            <a href="{{ route('contact.index') }}" class="{{ request()->routeIs('contact.index') || request()->routeIs('contact.show-submission') ? 'active' : '' }}">Contact Submissions</a>
        </aside>
    @endif
@endauth

