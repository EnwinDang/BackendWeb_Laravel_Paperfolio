@extends(auth()->user()->is_admin ? 'layouts.app' : 'layouts.dashboard')

@section('title', 'Edit Profile')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1>Edit Profile</h1>
        <a href="{{ route('profile.show', $user) }}" class="btn btn-secondary">← Back to Profile</a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="username">Username <span style="color: var(--gray); font-weight: normal;">(optional)</span></label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="{{ old('username', $user->username) }}"
                    placeholder="Choose a username to display on your profile"
                >
                <small style="color: var(--gray); font-size: 0.875rem; display: block; margin-top: 0.25rem;">
                    If left empty, your name will be displayed instead.
                </small>
            </div>

            <div class="form-group">
                <label for="date_of_birth">Date of Birth <span style="color: var(--gray); font-weight: normal;">(optional)</span></label>
                <input 
                    type="date" 
                    id="date_of_birth" 
                    name="date_of_birth" 
                    value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                    max="{{ now()->subYears(13)->format('Y-m-d') }}"
                >
                <small style="color: var(--gray); font-size: 0.875rem; display: block; margin-top: 0.25rem;">
                    Your age will be calculated and displayed on your profile.
                </small>
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture <span style="color: var(--gray); font-weight: normal;">(optional)</span></label>
                @if($user->profile_picture)
                    <div style="margin-bottom: 1rem;">
                        <p style="margin-bottom: 0.5rem; color: var(--gray); font-size: 0.95rem;"><strong>Current Profile Picture:</strong></p>
                        <img src="{{ $user->getProfilePictureUrl() }}" alt="Current profile picture" style="max-width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid var(--dark-blue); box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    </div>
                @endif
                <input 
                    type="file" 
                    id="profile_picture" 
                    name="profile_picture" 
                    accept="image/*"
                    onchange="previewImage(this)"
                >
                <div id="image-preview" style="margin-top: 1rem; display: none;">
                    <p style="margin-bottom: 0.5rem; color: var(--gray); font-size: 0.95rem;"><strong>Preview:</strong></p>
                    <img id="preview-img" src="" alt="Preview" style="max-width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid var(--dark-blue); box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                </div>
                <small style="color: var(--gray); font-size: 0.875rem; display: block; margin-top: 0.25rem;">
                    Maximum file size: 2MB. Recommended: Square image (e.g., 500x500px).
                </small>
            </div>

            <div class="form-group">
                <label for="about_me">About Me <span style="color: var(--gray); font-weight: normal;">(optional)</span></label>
                <textarea 
                    id="about_me" 
                    name="about_me" 
                    maxlength="1000" 
                    rows="6"
                    placeholder="Tell others about yourself..."
                    style="min-height: 120px; resize: vertical;"
                >{{ old('about_me', $user->about_me) }}</textarea>
                <small style="color: var(--gray); font-size: 0.875rem; display: block; margin-top: 0.25rem;">
                    <span id="char-count">{{ strlen(old('about_me', $user->about_me ?? '')) }}</span> / 1000 characters
                </small>
            </div>

            @if(!$user->is_admin)
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="show_portfolio" value="1" style="width: auto;" {{ old('show_portfolio', $user->show_portfolio) ? 'checked' : '' }}>
                        Let other users see my portfolio and trading history on my profile
                    </label>
                    <small style="color: var(--gray); font-size: 0.875rem; display: block; margin-top: 0.25rem;">
                        Turn this off to keep your trades and holdings private. Your own view and admins are unaffected.
                    </small>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="show_age" value="1" style="width: auto;" {{ old('show_age', $user->show_age) ? 'checked' : '' }}>
                        Show my date of birth and age on my public profile
                    </label>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="show_email" value="1" style="width: auto;" {{ old('show_email', $user->show_email) ? 'checked' : '' }}>
                        Show my email address on my public profile
                    </label>
                    <small style="color: var(--gray); font-size: 0.875rem; display: block; margin-top: 0.25rem;">
                        Your own view and admins can always see these regardless of these settings.
                    </small>
                </div>
            @endif

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Update Profile</button>
                <a href="{{ route('profile.show', $user) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    @if(!$user->is_admin)
        <div class="card" style="margin-top: 1.5rem;">
            <h3 style="margin-bottom: 0.25rem;">Restart Portfolio</h3>
            <p style="font-size: 0.85rem; color: var(--gray); margin-bottom: 1rem;">
                Wipes all your trades and leveraged positions and resets your cash to exactly $1,000. Cannot be undone.
            </p>
            <form method="POST" action="{{ route('portfolio.restart') }}" onsubmit="return confirmAction(event, 'This deletes all your trades and open positions and resets your cash to \$1,000. Are you sure?');">
                @csrf
                <button type="submit" class="btn btn-secondary">Restart Portfolio</button>
            </form>
        </div>
    @endif

    @push('scripts')
    <script>
        // Character counter for about_me textarea
        const aboutMeTextarea = document.getElementById('about_me');
        const charCount = document.getElementById('char-count');
        
        if (aboutMeTextarea && charCount) {
            aboutMeTextarea.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }

        // Image preview
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
    @endpush
@endsection
