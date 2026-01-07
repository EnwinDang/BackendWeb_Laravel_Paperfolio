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

        <div style="margin-bottom: 1.5rem;">
            <h3>Message</h3>
            <div style="background: var(--gray-light); padding: 1rem; border-radius: 6px; white-space: pre-wrap; line-height: 1.6;">
                {{ $contactSubmission->message }}
            </div>
        </div>

        @if($contactSubmission->admin_response)
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: #d1fae5; border: 1px solid #10b981; border-radius: 6px;">
                <h3 style="margin-top: 0; color: #065f46;">Admin Response</h3>
                <div style="white-space: pre-wrap; line-height: 1.6; color: #065f46;">
                    {{ $contactSubmission->admin_response }}
                </div>
                <div style="margin-top: 0.75rem; font-size: 0.875rem; color: #047857;">
                    Responded: {{ $contactSubmission->responded_at->format('F j, Y \a\t g:i A') }}
                </div>
            </div>
        @else
            <div class="card" style="margin-top: 1.5rem;">
                <h3>Send Response</h3>
                <form method="POST" action="{{ route('contact.respond', $contactSubmission) }}">
                    @csrf
                    <div class="form-group">
                        <label for="admin_response">Response *</label>
                        <textarea 
                            id="admin_response" 
                            name="admin_response" 
                            required 
                            style="min-height: 200px;"
                            placeholder="Type your response here. This will be sent to {{ $contactSubmission->email }}"
                        >{{ old('admin_response') }}</textarea>
                        @error('admin_response')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Send Response</button>
                </form>
            </div>
        @endif
    </div>
@endsection


