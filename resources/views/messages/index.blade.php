@extends('layouts.app')

@section('title', 'Messages')

@section('content')
    <h1>Messages</h1>

    @if($conversations->isEmpty())
        <div class="card">
            <div class="empty">
                <p>No conversations yet. Start a conversation by visiting someone's profile and clicking "Send Message".</p>
            </div>
        </div>
    @else
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Last Message</th>
                        <th>Time</th>
                        <th>Unread</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($conversations as $conversation)
                        @php
                            $otherUser = $conversation['user'];
                            $lastMessage = $conversation['last_message'];
                            $unreadCount = $conversation['unread_count'];
                        @endphp
                        <tr style="{{ $unreadCount > 0 ? 'background-color: #eff6ff;' : '' }}">
                            <td>
                                <a href="{{ route('messages.show', $otherUser) }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 0.75rem;">
                                    @if($otherUser->getProfilePictureUrl())
                                        <img src="{{ $otherUser->getProfilePictureUrl() }}" alt="{{ $otherUser->getDisplayName() }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                    @else
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--dark-blue); color: var(--white); display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                            {{ strtoupper(substr($otherUser->getDisplayName(), 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight: {{ $unreadCount > 0 ? 'bold' : 'normal' }};">{{ $otherUser->getDisplayName() }}</div>
                                        @if($otherUser->username)
                                            <div style="font-size: 0.875rem; color: var(--gray);">{{ $otherUser->username }}</div>
                                        @endif
                                    </div>
                                </a>
                            </td>
                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ \Illuminate\Support\Str::limit($lastMessage->message, 50) }}
                            </td>
                            <td style="color: var(--gray); font-size: 0.875rem;">
                                {{ $lastMessage->created_at->diffForHumans() }}
                            </td>
                            <td style="text-align: center;">
                                @if($unreadCount > 0)
                                    <span style="background: var(--dark-blue); color: var(--white); padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">
                                        {{ $unreadCount }}
                                    </span>
                                @else
                                    <span style="color: var(--gray);">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('messages.show', $otherUser) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Open</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

