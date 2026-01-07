@extends('layouts.app')

@section('title', 'Conversation with ' . $user->getDisplayName())

@section('content')
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('messages.index') }}" class="btn btn-secondary">← Back to Messages</a>
        <a href="{{ route('profile.show', $user) }}" class="btn btn-secondary" style="margin-left: 0.5rem;">View Profile</a>
    </div>

    <div class="card" style="margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--gray-light);">
            @if($user->getProfilePictureUrl())
                <img src="{{ $user->getProfilePictureUrl() }}" alt="{{ $user->getDisplayName() }}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
            @else
                <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--dark-blue); color: var(--white); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.25rem;">
                    {{ strtoupper(substr($user->getDisplayName(), 0, 1)) }}
                </div>
            @endif
            <div>
                <h2 style="margin: 0; color: var(--dark-blue);">{{ $user->getDisplayName() }}</h2>
                @if($user->username)
                    <div style="font-size: 0.875rem; color: var(--gray);">{{ $user->username }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="card" style="max-height: 500px; overflow-y: auto; margin-bottom: 1rem; padding: 1rem;">
        @if($messages->isEmpty())
            <div style="text-align: center; padding: 2rem; color: var(--gray);">
                <p>No messages yet. Start the conversation!</p>
            </div>
        @else
            @foreach($messages as $message)
                @php
                    $isSender = $message->sender_id === Auth::id();
                @endphp
                <div style="margin-bottom: 1.5rem; display: flex; {{ $isSender ? 'justify-content: flex-end;' : 'justify-content: flex-start;' }}">
                    <div style="max-width: 70%; {{ $isSender ? 'text-align: right;' : 'text-align: left;' }}">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem; {{ $isSender ? 'flex-direction: row-reverse;' : '' }}">
                            <span style="font-size: 0.875rem; color: var(--gray); font-weight: 500;">
                                {{ $message->sender->getDisplayName() }}
                            </span>
                            <span style="font-size: 0.75rem; color: var(--gray);">
                                {{ $message->created_at->format('M j, g:i A') }}
                            </span>
                        </div>
                        <div style="
                            padding: 0.75rem 1rem;
                            border-radius: 12px;
                            background: {{ $isSender ? 'var(--dark-blue)' : 'var(--gray-light)' }};
                            color: {{ $isSender ? 'var(--white)' : 'var(--gray-dark)' }};
                            display: inline-block;
                            word-wrap: break-word;
                            white-space: pre-wrap;
                        ">
                            {{ $message->message }}
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="card">
        <form method="POST" action="{{ route('messages.store', $user) }}">
            @csrf
            <div class="form-group">
                <label for="message">Send a message</label>
                <textarea 
                    id="message" 
                    name="message" 
                    required 
                    rows="4"
                    placeholder="Type your message here..."
                    style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; font-family: inherit; resize: vertical;"
                ></textarea>
                @error('message')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
    </div>
@endsection

