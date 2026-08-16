@extends(auth()->user()->is_admin ? 'layouts.app' : 'layouts.dashboard')

@section('title', 'Global Feed')

@push('styles')
<style>
    .post-card {
        border-bottom: 2px solid var(--ink);
        padding: 1rem 0;
    }
    .post-card:last-child {
        border-bottom: none;
    }
    .post-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 0.4rem;
    }
    .post-author {
        font-weight: 800;
        text-decoration: none;
        color: var(--ink);
        text-transform: uppercase;
        font-size: 0.85rem;
    }
    .post-time {
        color: var(--text-gray);
        font-size: 0.75rem;
    }
    .post-content {
        font-size: 0.95rem;
        margin-bottom: 0.6rem;
        white-space: pre-wrap;
    }
    .post-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .like-btn {
        background: none;
        border: 2px solid var(--ink);
        color: var(--ink);
        padding: 0.25rem 0.6rem;
        font-family: inherit;
        font-weight: 700;
        font-size: 0.8rem;
        cursor: pointer;
    }
    .like-btn.liked {
        background: var(--accent);
        color: var(--on-accent);
    }
    .cashtag {
        color: var(--ink);
        font-weight: 800;
        background: var(--accent-dim);
        padding: 0 0.2rem;
        text-decoration: none;
    }
</style>
@endpush

@section('content')
    @if(!auth()->user()->is_admin)
        <div class="card">
            <h2>Share your alpha</h2>
            <form method="POST" action="{{ route('feed.store') }}">
                @csrf
                <textarea name="content" rows="3" maxlength="500" placeholder="What's happening? Use $cashtags like $BTC to link a market..." required style="margin-bottom: 0.75rem;"></textarea>
                <button type="submit" class="btn btn-primary">Post</button>
            </form>
        </div>
    @endif

    <div class="card" style="margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('feed.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 160px;">
                <label for="user">User</label>
                <input type="text" id="user" name="user" value="{{ request('user') }}" placeholder="Search by name or username...">
            </div>
            <div class="form-group" style="margin-bottom: 0; flex: 2; min-width: 220px;">
                <label for="q">Text</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Search post content...">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('user') || request('q'))
                <a href="{{ route('feed.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>

    <div class="card">
        <h2>Global Feed</h2>
        @if($posts->isEmpty())
            <div class="empty">
                <p>
                    @if(request('user') || request('q'))
                        No posts match your search.
                    @else
                        No posts yet. Be the first to share something.
                    @endif
                </p>
            </div>
        @else
            @foreach($posts as $post)
                <div class="post-card">
                    <div class="post-header">
                        <a href="{{ route('profile.show', $post->user) }}" class="post-author">{{ $post->user->getDisplayName() }}</a>
                        <span class="post-time">{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="post-content">{!! $post->renderedContent() !!}</div>
                    <div class="post-actions">
                        @if(auth()->check() && !auth()->user()->is_admin)
                            @php $liked = $post->isLikedBy(auth()->user()); @endphp
                            <button type="button" class="like-btn {{ $liked ? 'liked' : '' }}" data-post-id="{{ $post->id }}" data-liked="{{ $liked ? '1' : '0' }}" onclick="toggleLike(this)">
                                &hearts; <span class="like-count">{{ $post->likers_count }}</span>
                            </button>
                        @else
                            <span class="like-btn">&hearts; {{ $post->likers_count }}</span>
                        @endif

                        @auth
                            @if($post->user_id === auth()->id())
                                <form method="POST" action="{{ route('feed.destroy', $post) }}" onsubmit="return confirmAction(event, 'Delete this post?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="like-btn">Delete</button>
                                </form>
                            @elseif(auth()->user()->is_admin)
                                <form method="POST" action="{{ route('feed.destroy', $post) }}" onsubmit="return false;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="reason" value="">
                                    <input type="hidden" name="custom_reason" value="">
                                    <button type="button" class="like-btn" onclick="openDeleteReasonModal(this.closest('form'))">Delete</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            @endforeach

            <div style="margin-top: 1rem;">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

    @if(auth()->check() && !auth()->user()->is_admin)
        <script>
            function toggleLike(btn) {
                var postId = btn.dataset.postId;
                var liked = btn.dataset.liked === '1';
                var url = '{{ url('/feed') }}/' + postId + '/' + (liked ? 'unlike' : 'like');

                btn.disabled = true;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        btn.dataset.liked = data.liked ? '1' : '0';
                        btn.classList.toggle('liked', data.liked);
                        btn.querySelector('.like-count').textContent = data.count;
                    })
                    .finally(function () {
                        btn.disabled = false;
                    });
            }
        </script>
    @endif

    @if(auth()->user()->is_admin)
        <div id="delete-reason-backdrop" class="confirm-modal-backdrop" onclick="if (event.target === this) closeDeleteReasonModal();">
            <div class="confirm-modal" role="alertdialog" aria-modal="true">
                <p style="margin-bottom: 0.75rem; font-weight: 800;">Why are you removing this post?</p>
                <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem;">
                    @foreach(\App\Models\Post::DELETION_REASONS as $key => $label)
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 500; text-transform: none; cursor: pointer;">
                            <input type="radio" name="delete_reason_choice" value="{{ $key }}" onchange="onDeleteReasonChange()">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                <textarea id="delete-reason-custom" placeholder="Describe the reason..." maxlength="500" rows="3" style="display: none; margin-bottom: 0.75rem; width: 100%;"></textarea>
                <p id="delete-reason-error" style="display: none; color: var(--error); font-size: 0.8rem; margin-bottom: 0.75rem;"></p>
                <div class="confirm-modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteReasonModal()">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="submitDeleteReason()">Delete Post</button>
                </div>
            </div>
        </div>

        <script>
            var _deleteReasonForm = null;

            function openDeleteReasonModal(form) {
                _deleteReasonForm = form;
                document.querySelectorAll('input[name="delete_reason_choice"]').forEach(function (r) { r.checked = false; });
                document.getElementById('delete-reason-custom').style.display = 'none';
                document.getElementById('delete-reason-custom').value = '';
                document.getElementById('delete-reason-error').style.display = 'none';
                document.getElementById('delete-reason-backdrop').classList.add('open');
            }

            function onDeleteReasonChange() {
                var selected = document.querySelector('input[name="delete_reason_choice"]:checked');
                document.getElementById('delete-reason-custom').style.display = (selected && selected.value === 'other') ? 'block' : 'none';
                document.getElementById('delete-reason-error').style.display = 'none';
            }

            function closeDeleteReasonModal() {
                document.getElementById('delete-reason-backdrop').classList.remove('open');
                _deleteReasonForm = null;
            }

            function submitDeleteReason() {
                var selected = document.querySelector('input[name="delete_reason_choice"]:checked');
                var errorEl = document.getElementById('delete-reason-error');

                if (!selected) {
                    errorEl.textContent = 'Please select a reason.';
                    errorEl.style.display = 'block';
                    return;
                }

                var customReason = document.getElementById('delete-reason-custom').value.trim();
                if (selected.value === 'other' && !customReason) {
                    errorEl.textContent = 'Please describe the reason.';
                    errorEl.style.display = 'block';
                    return;
                }

                if (_deleteReasonForm) {
                    _deleteReasonForm.querySelector('input[name="reason"]').value = selected.value;
                    _deleteReasonForm.querySelector('input[name="custom_reason"]').value = customReason;
                    _deleteReasonForm.submit();
                }
            }
        </script>
    @endif
@endsection
