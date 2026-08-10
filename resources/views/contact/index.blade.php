@extends('layouts.app')

@section('title', 'Contact Submissions')

@section('content')
    <h1>Contact Submissions</h1>

    @if($submissions->isEmpty())
        <div class="card">
            <div class="empty">
                <p>No contact submissions yet.</p>
            </div>
        </div>
    @else
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Response</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $submission)
                        <tr style="{{ !$submission->read ? 'background-color: var(--soft-blue-bg);' : '' }}">
                            <td>{{ $submission->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $submission->name }}</td>
                            <td>{{ $submission->email }}</td>
                            <td>{{ $submission->subject }}</td>
                            <td>
                                @if($submission->read)
                                    <span style="color: var(--gray);">Read</span>
                                @else
                                    <strong style="color: var(--dark-blue);">Unread</strong>
                                @endif
                            </td>
                            <td>
                                @if($submission->admin_response)
                                    <span style="color: var(--success);">✓ Responded</span>
                                    <br>
                                    <small style="color: var(--gray);">{{ $submission->responded_at->format('Y-m-d H:i') }}</small>
                                @else
                                    <span style="color: var(--gray);">No response</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('contact.show-submission', $submission) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; margin-right: 0.25rem;">View</a>
                                @if($submission->read)
                                    <form method="POST" action="{{ route('contact.mark-unread', $submission) }}" style="display: inline; margin-right: 0.25rem;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Mark Unread</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('contact.mark-read', $submission) }}" style="display: inline; margin-right: 0.25rem;">
                                        @csrf
                                        <button type="submit" class="btn btn-success" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Mark Read</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('contact.destroy', $submission) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this submission?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection


