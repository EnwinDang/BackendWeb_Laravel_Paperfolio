@extends(auth()->user()->is_admin ? 'layouts.app' : 'layouts.dashboard')

@section('title', 'Notifications')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h1 style="margin-bottom: 0;">Notifications</h1>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Mark all read</button>
            </form>
        @endif
    </div>

    <div class="card">
        @if($notifications->isEmpty())
            <div class="empty"><p>No notifications yet.</p></div>
        @else
            @foreach($notifications as $notification)
                <div class="post-card" style="border-bottom: 2px solid var(--ink); padding: 1rem 0; {{ $notification->read_at ? 'opacity: 0.6;' : '' }}">
                    @if($notification->type === \App\Notifications\PostRemoved::class)
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.4rem;">
                            <strong>Your post was removed</strong>
                            <span style="color: var(--text-gray); font-size: 0.75rem;">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p style="font-size: 0.85rem; color: var(--text-gray); margin-bottom: 0.4rem;">
                            Reason: <strong>{{ $notification->data['reason'] }}</strong>
                            @if($notification->data['custom_reason'])
                                &mdash; {{ $notification->data['custom_reason'] }}
                            @endif
                        </p>
                        <p style="font-size: 0.85rem; font-style: italic; color: var(--text-gray); border-left: 2px solid var(--ink); padding-left: 0.6rem;">
                            &ldquo;{{ \Illuminate\Support\Str::limit($notification->data['post_content'], 140) }}&rdquo;
                        </p>
                    @elseif($notification->type === \App\Notifications\NewContactSubmission::class)
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.4rem;">
                            <strong>New contact form submission</strong>
                            <span style="color: var(--text-gray); font-size: 0.75rem;">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p style="font-size: 0.85rem; margin-bottom: 0.4rem;">
                            <strong>{{ $notification->data['name'] }}</strong> ({{ $notification->data['email'] }}) &mdash; {{ $notification->data['subject'] }}
                        </p>
                        <p style="font-size: 0.85rem; font-style: italic; color: var(--text-gray); border-left: 2px solid var(--ink); padding-left: 0.6rem; margin-bottom: 0.4rem;">
                            &ldquo;{{ \Illuminate\Support\Str::limit($notification->data['message'], 140) }}&rdquo;
                        </p>
                        <a href="{{ route('contact.show-submission', $notification->data['submission_id']) }}" style="font-size: 0.8rem; font-weight: 700;">View submission &rarr;</a>
                    @else
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.4rem;">
                            <strong>Notification</strong>
                            <span style="color: var(--text-gray); font-size: 0.75rem;">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    @endif

                    @if(!$notification->read_at)
                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}" style="margin-top: 0.5rem;">
                            @csrf
                            <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Mark read</button>
                        </form>
                    @endif
                </div>
            @endforeach

            <div style="margin-top: 1rem;">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection
