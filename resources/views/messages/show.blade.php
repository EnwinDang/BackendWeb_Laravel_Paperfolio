@extends('layouts.dashboard')

@section('title', 'Conversation with ' . $user->getDisplayName())

@push('styles')
<style>
    .messages-layout {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 0;
        height: calc(100vh - 200px);
        min-height: 600px;
        border: 2px solid var(--ink);
        box-shadow: 4px 4px 0 var(--ink);
        overflow: hidden;
        background: var(--white);
    }
    .conversations-sidebar {
        border-right: 2px solid var(--ink);
        background: var(--gray-light);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .sidebar-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        background: var(--white);
    }
    .sidebar-header h2 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark-blue);
    }
    .conversations-list {
        flex: 1;
        overflow-y: auto;
        padding: 0.5rem;
    }
    .conversations-list::-webkit-scrollbar {
        width: 6px;
    }
    .conversations-list::-webkit-scrollbar-track {
        background: transparent;
    }
    .conversations-list::-webkit-scrollbar-thumb {
        background: var(--gray);
        border-radius: 3px;
    }
    .conversation-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 8px;
        text-decoration: none;
        color: inherit;
        transition: background 0.2s;
        margin-bottom: 0.25rem;
    }
    .conversation-item:hover {
        background: var(--white);
    }
    .conversation-item.active {
        background: var(--white);
        border: 1px solid var(--dark-blue-light);
    }
    .conversation-item.unread {
        background: var(--accent-dim);
    }
    .conversation-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .conversation-avatar-placeholder {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--dark-blue);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .conversation-info {
        flex: 1;
        min-width: 0;
    }
    .conversation-name {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--dark-blue);
        margin: 0 0 0.25rem 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .conversation-item.unread .conversation-name {
        font-weight: 700;
    }
    .conversation-preview {
        font-size: 0.85rem;
        color: var(--gray);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin: 0;
    }
    .conversation-item.unread .conversation-preview {
        color: var(--dark-blue);
        font-weight: 500;
    }
    .unread-badge {
        background: var(--dark-blue);
        color: var(--white);
        padding: 0.125rem 0.5rem;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 700;
        min-width: 20px;
        text-align: center;
        flex-shrink: 0;
    }
    .chat-container {
        display: flex;
        flex-direction: column;
        background: var(--white);
        overflow: hidden;
    }
    .chat-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 1rem;
        background: var(--white);
    }
    .chat-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .chat-avatar-placeholder {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--dark-blue);
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .chat-user-info h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: var(--dark-blue);
    }
    .chat-user-info p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--gray);
    }
    .messages-area {
        flex: 1;
        overflow-y: auto;
        padding: 1rem 1.5rem;
        background: var(--white);
    }
    .messages-area::-webkit-scrollbar {
        width: 8px;
    }
    .messages-area::-webkit-scrollbar-track {
        background: transparent;
    }
    .messages-area::-webkit-scrollbar-thumb {
        background: var(--gray);
        border-radius: 4px;
    }
    .messages-area::-webkit-scrollbar-thumb:hover {
        background: var(--gray);
    }
    .date-separator {
        text-align: center;
        margin: 1.5rem 0;
        position: relative;
    }
    .date-separator::before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 50%;
        height: 1px;
        background: #e2e8f0;
    }
    .date-separator span {
        background: var(--white);
        padding: 0 0.75rem;
        color: var(--gray);
        font-size: 0.8rem;
        position: relative;
    }
    .message-group {
        margin-bottom: 0.75rem;
        display: flex;
        flex-direction: column;
    }
    .message-item {
        display: flex;
        margin-bottom: 0.5rem;
    }
    .message-item.sent {
        justify-content: flex-end;
    }
    .message-item.received {
        justify-content: flex-start;
    }
    .message-bubble {
        max-width: 65%;
        display: inline-block;
        vertical-align: bottom;
    }
    .message-content {
        padding: 0.5rem 0.75rem;
        border-radius: 16px;
        word-wrap: break-word;
        white-space: pre-wrap;
        line-height: 1.4;
        font-size: 0.9rem;
        display: inline-block;
        max-width: 100%;
    }
    .message-item.received .message-content {
        background: var(--gray-light);
        color: var(--gray-dark);
        border-bottom-left-radius: 4px;
    }
    .message-item.sent .message-content {
        background: var(--dark-blue);
        color: var(--white);
        border-bottom-right-radius: 4px;
    }
    .message-time {
        font-size: 0.7rem;
        color: var(--gray);
        margin-top: 0.25rem;
        padding: 0 0.5rem;
        white-space: nowrap;
    }
    .message-item.sent .message-time {
        text-align: right;
    }
    .message-item.received .message-time {
        text-align: left;
    }
    .empty-messages {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--gray);
    }
    .empty-messages-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }
    .empty-messages h3 {
        color: var(--dark-blue);
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    .message-input-area {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e2e8f0;
        background: var(--white);
    }
    .message-form {
        display: flex;
        gap: 0.75rem;
        align-items: flex-end;
    }
    .message-textarea {
        flex: 1;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        font-size: 0.9rem;
        font-family: inherit;
        resize: none;
        transition: border-color 0.2s;
        line-height: 1.4;
        max-height: 120px;
    }
    .message-textarea:focus {
        outline: none;
        border-color: var(--dark-blue-light);
    }
    .message-send-btn {
        padding: 0.75rem 1.5rem;
        background: var(--dark-blue);
        color: var(--white);
        border: none;
        border-radius: 20px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        flex-shrink: 0;
    }
    .message-send-btn:hover {
        background: var(--dark-blue-hover);
    }
    @media (max-width: 1024px) {
        .messages-layout {
            grid-template-columns: 280px 1fr;
        }
    }
    @media (max-width: 768px) {
        .messages-layout {
            grid-template-columns: 1fr;
            height: auto;
            min-height: 500px;
        }
        .conversations-sidebar {
            display: none;
        }
        .message-bubble {
            max-width: 75%;
        }
    }
</style>
@endpush

@section('content')
    <div class="messages-layout">
        <div class="conversations-sidebar">
            <div class="sidebar-header">
                <h2>Messages</h2>
            </div>
            <div class="conversations-list">
                @if($conversations->isEmpty())
                    <div style="padding: 2rem 1rem; text-align: center; color: var(--gray); font-size: 0.9rem;">
                        No conversations yet
                    </div>
                @else
                    @foreach($conversations as $conversation)
                        @php
                            $otherUser = $conversation['user'];
                            $lastMessage = $conversation['last_message'];
                            $unreadCount = $conversation['unread_count'];
                            $isActive = $otherUser->id === $user->id;
                        @endphp
                        <a href="{{ route('messages.show', $otherUser) }}" class="conversation-item {{ $isActive ? 'active' : '' }} {{ $unreadCount > 0 ? 'unread' : '' }}">
                            @if($otherUser->getProfilePictureUrl())
                                <img src="{{ $otherUser->getProfilePictureUrl() }}" alt="{{ $otherUser->getDisplayName() }}" class="conversation-avatar">
                            @else
                                <div class="conversation-avatar-placeholder">
                                    {{ strtoupper(substr($otherUser->getDisplayName(), 0, 1)) }}
                                </div>
                            @endif
                            <div class="conversation-info">
                                <div class="conversation-name">{{ $otherUser->getDisplayName() }}</div>
                                <p class="conversation-preview">{{ \Illuminate\Support\Str::limit($lastMessage->message, 40) }}</p>
                            </div>
                            @if($unreadCount > 0)
                                <span class="unread-badge">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="chat-container">
            <div class="chat-header">
                @if($user->getProfilePictureUrl())
                    <img src="{{ $user->getProfilePictureUrl() }}" alt="{{ $user->getDisplayName() }}" class="chat-avatar">
                @else
                    <div class="chat-avatar-placeholder">
                        {{ strtoupper(substr($user->getDisplayName(), 0, 1)) }}
                    </div>
                @endif
                <div class="chat-user-info">
                    <h3>{{ $user->getDisplayName() }}</h3>
                    @if($user->username)
                        <p>{{ $user->username }}</p>
                    @endif
                </div>
            </div>

            <div class="messages-area">
                @if($messages->isEmpty())
                    <div class="empty-messages">
                        <div class="empty-messages-icon">💬</div>
                        <h3>No messages yet</h3>
                        <p>Start the conversation by sending a message below</p>
                    </div>
                @else
                    @foreach($messagesByDate as $date => $dateMessages)
                        <div class="date-separator">
                            <span>
                                @php
                                    $dateObj = \Carbon\Carbon::parse($date);
                                    if ($dateObj->isToday()) {
                                        echo 'Today';
                                    } elseif ($dateObj->isYesterday()) {
                                        echo 'Yesterday';
                                    } else {
                                        echo $dateObj->format('M j, Y');
                                    }
                                @endphp
                            </span>
                        </div>
                        @foreach($dateMessages as $message)
                            @php
                                $isSender = $message->sender_id === Auth::id();
                            @endphp
                            <div class="message-item {{ $isSender ? 'sent' : 'received' }}">
                                <div class="message-bubble">
                                    <div class="message-content">{{ $message->message }}</div>
                                    <div class="message-time">{{ $message->created_at->format('g:i A') }}</div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @endif
            </div>

            <div class="message-input-area">
                <form method="POST" action="{{ route('messages.store', $user) }}" class="message-form">
                    @csrf
                    <textarea 
                        id="message" 
                        name="message" 
                        required 
                        rows="1"
                        placeholder="Type a message..."
                        class="message-textarea"
                        oninput="this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px';"
                    ></textarea>
                    <button type="submit" class="message-send-btn">Send</button>
                </form>
                @error('message')
                    <div style="margin-top: 0.5rem; color: var(--error); font-size: 0.85rem;">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
@endsection
