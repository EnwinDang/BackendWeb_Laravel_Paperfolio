@extends(auth()->check() ? 'layouts.dashboard' : 'layouts.app')

@section('title', 'Contact Us')

@section('content')
    <h1>Contact Us</h1>

    <div class="card">
        <form method="POST" action="{{ route('contact.submit') }}">
            @csrf

            <div class="form-group">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="subject">Subject *</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required>
            </div>

            <div class="form-group">
                <label for="message">Message *</label>
                <textarea id="message" name="message" required style="min-height: 150px;">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
    </div>
@endsection
