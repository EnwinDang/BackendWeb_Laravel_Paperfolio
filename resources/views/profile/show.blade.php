@extends(!auth()->check() || auth()->user()->is_admin ? 'layouts.app' : 'layouts.dashboard')

@section('title', $user->getDisplayName() . ' - Profile')

@section('content')
    <div class="card">
        <div style="display: flex; gap: 2rem; margin-bottom: 2rem; flex-wrap: wrap; align-items: flex-start;">
            <div style="flex-shrink: 0;">
                @if($user->profile_picture)
                    <img src="{{ $user->getProfilePictureUrl() }}" alt="{{ $user->getDisplayName() }}'s Profile Picture" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid var(--dark-blue); box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                @else
                    <div style="width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, var(--dark-blue) 0%, var(--dark-blue-light) 100%); display: flex; align-items: center; justify-content: center; border: 3px solid var(--dark-blue); color: var(--white); font-size: 3rem; font-weight: bold; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        {{ strtoupper(substr($user->getDisplayName(), 0, 1)) }}
                    </div>
                @endif
            </div>
            <div style="flex: 1; min-width: 200px;">
                <h1 style="margin: 0 0 0.5rem 0; color: var(--dark-blue);">{{ $user->getDisplayName() }}</h1>
                @if(Auth::id() === $user->id || auth()->check() && auth()->user()->is_admin || $user->show_email)
                    <p style="color: var(--gray); margin-bottom: 0.75rem; font-size: 0.95rem;">
                        <strong>Email:</strong> {{ $user->email }}
                    </p>
                @endif
                @if($user->date_of_birth && (Auth::id() === $user->id || auth()->check() && auth()->user()->is_admin || $user->show_age))
                    <p style="color: var(--gray); margin-bottom: 0.75rem; font-size: 0.95rem;">
                        <strong>Date of Birth:</strong> {{ $user->date_of_birth->format('F j, Y') }}
                        @php
                            $age = $user->date_of_birth->age;
                        @endphp
                        <span style="color: var(--gray);">({{ $age }} {{ Str::plural('year', $age) }} old)</span>
                    </p>
                @endif
                @if($portfolioValue !== null)
                    <div style="display: inline-block; padding: 0.5rem 1rem; background: var(--gray-light); border-radius: 6px; margin-bottom: 0.75rem;">
                        <span style="color: var(--gray); font-size: 0.85rem;">Portfolio Value:</span>
                        <strong style="color: var(--dark-blue); font-size: 1.15rem; margin-left: 0.25rem;">${{ number_format($portfolioValue, 2) }}</strong>
                    </div>
                @endif
                <p style="color: var(--gray); margin-bottom: 0.75rem; font-size: 0.95rem;">
                    <strong>Trading:</strong>
                    @if($firstTradeAt)
                        Since {{ $firstTradeAt->format('F Y') }} ({{ $firstTradeAt->diffForHumans(null, true) }})
                    @else
                        Hasn't made a trade yet
                    @endif
                </p>
                @auth
                    @if(Auth::id() === $user->id)
                        <div style="margin-top: 1rem;">
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                        </div>
                    @elseif(!$user->is_admin)
                        <div style="margin-top: 1rem;">
                            <a href="{{ route('messages.show', $user) }}" class="btn btn-primary">Send Private Message</a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        @if($user->about_me)
            <div style="border-top: 1px solid var(--gray-light); padding-top: 1.5rem; margin-top: 1.5rem;">
                <h2 style="margin-bottom: 1rem; color: var(--dark-blue);">About Me</h2>
                <div style="line-height: 1.8; color: var(--gray-dark); white-space: pre-wrap;">{{ $user->about_me }}</div>
            </div>
        @else
            @auth
                @if(Auth::id() === $user->id)
                    <div style="border-top: 1px solid var(--gray-light); padding-top: 1.5rem; margin-top: 1.5rem; text-align: center; color: var(--gray);">
                        <p>You haven't added an "About Me" section yet.</p>
                        <a href="{{ route('profile.edit') }}" class="btn btn-secondary" style="margin-top: 0.5rem;">Add About Me</a>
                    </div>
                @endif
            @endauth
        @endif
    </div>

    {{-- Trades Section --}}
    <div class="card" style="margin-top: 2rem;">
        <h2 style="margin-bottom: 1rem; color: var(--dark-blue);">Trading History</h2>

        @guest
            {{-- Not logged in: show message to log in --}}
            <div style="text-align: center; padding: 2rem; color: var(--gray);">
                <p style="margin-bottom: 1rem;">Log in to view user trades</p>
                <a href="{{ route('login') }}" class="btn btn-primary">Log In</a>
            </div>
        @elseif(!$canViewPortfolio)
            <div style="text-align: center; padding: 2rem; color: var(--gray);">
                <p>{{ $user->getDisplayName() }} has kept their trading history and portfolio private.</p>
            </div>
        @else
            {{-- Show trades for logged-in users allowed to view this portfolio --}}
            @if($trades->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Asset</th>
                            <th>Amount</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trades as $trade)
                            <tr>
                                <td>{{ $trade->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <strong style="color: {{ $trade->type === 'buy' ? 'var(--success)' : 'var(--error)' }}">
                                        {{ strtoupper($trade->type) }}
                                    </strong>
                                </td>
                                <td>{{ $trade->asset->symbol }}</td>
                                <td class="price">{{ number_format($trade->amount, 8) }}</td>
                                <td class="price">${{ number_format($trade->price_snapshot, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty">
                    <p>No trades yet.</p>
                </div>
            @endif
        @endguest
    </div>
@endsection
