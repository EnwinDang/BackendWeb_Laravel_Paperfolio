<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Management</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 1000px; margin: 2rem auto; padding: 0 1rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; }
        .btn { display: inline-block; padding: 0.25rem 0.5rem; background: #1b1b18; color: white; text-decoration: none; border-radius: 4px; font-size: 0.875rem; }
        .btn-danger { background: #dc3545; }
    </style>
</head>
<body>
    @include('partials.admin-nav')

    <h1>User Management</h1>
    <a href="{{ route('users.create') }}" class="btn">Create User</a>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin: 1rem 0;">
            {{ session('success') }}
        </div>
    @endif

    <table style="margin-top: 2rem;">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
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
                        <form method="POST" action="{{ route('users.toggle-admin', $user) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn">{{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

