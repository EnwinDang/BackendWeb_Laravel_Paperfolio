@extends('layouts.app')

@section('title', 'Contact Submission')

@section('content')
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('contact.index') }}" class="btn btn-secondary">← Back to Submissions</a>
    </div>

    <div class="card">
        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h1 style="margin: 0;">{{ $contactSubmission->subject }}</h1>
                <div>
                    @if($contactSubmission->read)
                        <form method="POST" action="{{ route('contact.mark-unread', $contactSubmission) }}" style="display: inline; margin-right: 0.5rem;">
                            @csrf
                            <button type="submit" class="btn btn-secondary">Mark Unread</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('contact.mark-read', $contactSubmission) }}" style="display: inline; margin-right: 0.5rem;">
                            @csrf
                            <button type="submit" class="btn btn-success">Mark Read</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('contact.destroy', $contactSubmission) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this submission?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
            <div style="color: var(--gray); font-size: 0.875rem;">
                Received: {{ $contactSubmission->created_at->format('F j, Y \a\t g:i A') }}
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <h3>From</h3>
            <p><strong>Name:</strong> {{ $contactSubmission->name }}</p>
            <p><strong>Email:</strong> <a href="mailto:{{ $contactSubmission->email }}">{{ $contactSubmission->email }}</a></p>
        </div>

        <div>
            <h3>Message</h3>
            <div style="background: var(--gray-light); padding: 1rem; border-radius: 6px; white-space: pre-wrap; line-height: 1.6;">
                {{ $contactSubmission->message }}
            </div>
        </div>
    </div>
@endsection

