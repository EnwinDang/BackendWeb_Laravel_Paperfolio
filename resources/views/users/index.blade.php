@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <h1>User Management</h1>
    
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('users.create') }}" class="btn btn-primary">Create User</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->is_admin ? 'Admin' : 'User' }}</td>
                        <td>
                            @if($user->is_anonymized)
                                <span class="pill" style="background: var(--soft-red-bg); color: var(--soft-red-text);">Erased</span>
                            @elseif($user->is_suspended)
                                <span class="pill" style="background: var(--soft-amber-bg); color: var(--soft-amber-text);">Suspended</span>
                            @else
                                <span class="pill" style="background: var(--soft-green-bg); color: var(--soft-green-text);">Active</span>
                            @endif
                        </td>
                        <td>
                            @if($user->id === auth()->id())
                                <span style="color: var(--text-gray); font-size: 0.85rem;">This is you</span>
                            @elseif($user->is_anonymized)
                                <span style="color: var(--text-gray); font-size: 0.85rem;">No actions available</span>
                            @else
                                <form method="POST" action="{{ route('users.toggle-admin', $user) }}" style="display: inline;"
                                    onsubmit="return confirmAction(event, '{{ $user->is_admin ? 'Remove admin access from ' . addslashes($user->name) . '?' : 'Make ' . addslashes($user->name) . ' an admin? They will be able to manage users, news, FAQ, and contact submissions.' }}');">
                                    @csrf
                                    <button type="submit" class="btn {{ $user->is_admin ? 'btn-secondary' : 'btn-primary' }}" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                        {{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('users.toggle-suspend', $user) }}" style="display: inline;"
                                    onsubmit="return confirmAction(event, '{{ $user->is_suspended ? 'Unsuspend ' . addslashes($user->name) . '? They will be able to log in again.' : 'Suspend ' . addslashes($user->name) . '? They will be logged out and unable to log back in until unsuspended.' }}');">
                                    @csrf
                                    <button type="submit" class="btn {{ $user->is_suspended ? 'btn-secondary' : 'btn-danger' }}" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                        {{ $user->is_suspended ? 'Unsuspend' : 'Suspend' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('users.erase', $user) }}" style="display: inline;"
                                    onsubmit="return confirmAction(event, 'Permanently erase {{ addslashes($user->name) }}\'s personal data (name, email, photo, bio)? Their trades and posts stay but are no longer linked to their identity. This cannot be undone.');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                        Erase Data
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
