@extends('layouts.app')

@section('title', 'Messages')

@push('styles')
<style>
    .messages-header {
        margin-bottom: 2rem;
    }
    .messages-header h1 {
        margin: 0 0 0.5rem 0;
        color: var(--dark-blue);
        font-size: 2rem;
        font-weight: 700;
    }
    .messages-header p {
        margin: 0;
        color: var(--gray);
        font-size: 0.95rem;
    }
    .conversations-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .conversation-item {
        background: var(--white);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem;
        transition: all 0.2s ease;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .conversation-item:hover {
        border-color: var(--dark-blue-light);
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.1);
        transform: translateY(-2px);
    }
    .conversation-item.unread {
        background: #eff6ff;
        border-color: var(--dark-blue-light);
        border-width: 2px;
    }
    .conversation-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .conversation-avatar {
        flex-shrink: 0;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
    }
    .conversation-avatar-placeholder {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--dark-blue);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        border: 2px solid #e2e8f0;
    }
    .conversation-details {
        flex: 1;
        min-width: 0;
    }
    .conversation-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        gap: 1rem;
    }
    .conversation-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark-blue);
        margin: 0;
    }
    .conversation-item.unread .conversation-name {
        font-weight: 700;
    }
    .conversation-time {
        font-size: 0.875rem;
        color: var(--gray);
        white-space: nowrap;
        flex-shrink: 0;
    }
    .conversation-preview {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .conversation-message {
        font-size: 0.95rem;
        color: var(--gray-dark);
        margin: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex: 1;
        min-width: 0;
    }
    .conversation-item.unread .conversation-message {
        color: var(--dark-blue);
        font-weight: 500;
    }
    .unread-badge {
        background: var(--dark-blue);
        color: var(--white);
        padding: 0.25rem 0.625rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        min-width: 24px;
        text-align: center;
        flex-shrink: 0;
    }
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }
    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }
    .empty-state h2 {
        color: var(--dark-blue);
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
    .empty-state p {
        color: var(--gray);
        font-size: 1rem;
        line-height: 1.6;
        max-width: 500px;
        margin: 0 auto 1.5rem;
    }
    @media (max-width: 768px) {
        .conversation-content {
            gap: 0.75rem;
        }
        .conversation-avatar,
        .conversation-avatar-placeholder {
            width: 48px;
            height: 48px;
            font-size: 1rem;
        }
        .conversation-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
        }
        .conversation-time {
            font-size: 0.8rem;
        }
    }
</style>
@endpush

@section('content')
    <div class="messages-header">
        <h1>Messages</h1>
        <p>Manage your conversations and stay connected with other traders</p>
    </div>

    @if($conversations->isEmpty())
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon">💬</div>
                <h2>No conversations yet</h2>
                <p>Start a conversation by visiting someone's profile and clicking "Send Private Message"</p>
            </div>
        </div>
    @else
        <div class="conversations-list">
            @foreach($conversations as $conversation)
                @php
                    $otherUser = $conversation['user'];
                    $lastMessage = $conversation['last_message'];
                    $unreadCount = $conversation['unread_count'];
                @endphp
                <a href="{{ route('messages.show', $otherUser) }}" class="conversation-item {{ $unreadCount > 0 ? 'unread' : '' }}">
                    <div class="conversation-content">
                        @if($otherUser->getProfilePictureUrl())
                            <img src="{{ $otherUser->getProfilePictureUrl() }}" alt="{{ $otherUser->getDisplayName() }}" class="conversation-avatar">
                        @else
                            <div class="conversation-avatar-placeholder">
                                {{ strtoupper(substr($otherUser->getDisplayName(), 0, 1)) }}
                            </div>
                        @endif
                        <div class="conversation-details">
                            <div class="conversation-header">
                                <h3 class="conversation-name">{{ $otherUser->getDisplayName() }}</h3>
                                <span class="conversation-time">{{ $lastMessage->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="conversation-preview">
                                <p class="conversation-message">{{ \Illuminate\Support\Str::limit($lastMessage->message, 80) }}</p>
                                @if($unreadCount > 0)
                                    <span class="unread-badge">{{ $unreadCount }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
