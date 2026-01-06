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
                                <button type="submit" class="btn {{ $user->is_admin ? 'btn-secondary' : 'btn-primary' }}">
                                    {{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
