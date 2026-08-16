<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_suspended',
        'is_anonymized',
        'username',
        'date_of_birth',
        'profile_picture',
        'about_me',
        'show_portfolio',
        'show_age',
        'show_email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_suspended' => 'boolean',
            'is_anonymized' => 'boolean',
            'date_of_birth' => 'date',
            'show_portfolio' => 'boolean',
            'show_age' => 'boolean',
            'show_email' => 'boolean',
        ];
    }

    /**
     * Whether $viewer is allowed to see this user's portfolio/trading history.
     * Owners and admins can always see it; other users only if the flag is on.
     */
    public function portfolioVisibleTo(?User $viewer): bool
    {
        if ($viewer && ($viewer->id === $this->id || $viewer->is_admin)) {
            return true;
        }

        return $this->show_portfolio;
    }

    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function newsComments()
    {
        return $this->hasMany(\App\Models\NewsComment::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    /**
     * Many-to-many relationship: Users can watch multiple assets
     */
    public function watchedAssets()
    {
        return $this->belongsToMany(Asset::class, 'asset_user')->withTimestamps();
    }

    /**
     * Get the profile picture URL
     */
    public function getProfilePictureUrl()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        return null;
    }

    /**
     * Get the display name (username or fallback to name)
     */
    public function getDisplayName()
    {
        return $this->username ?? $this->name;
    }

    /**
     * Get the available cash balance
     * Users start with $1000 and can only trade with that amount
     */
    public function getCashBalance(): float
    {
        $initialBalance = 1000.0;

        // Calculate total spent on buy trades
        $totalSpent = $this->trades()
            ->where('type', 'buy')
            ->get()
            ->sum(function ($trade) {
                return $trade->amount * $trade->price_snapshot;
            });

        // Calculate total received from sell trades
        $totalReceived = $this->trades()
            ->where('type', 'sell')
            ->get()
            ->sum(function ($trade) {
                return $trade->amount * $trade->price_snapshot;
            });

        // Margin currently locked in open leveraged positions is unavailable
        $lockedMargin = $this->positions()
            ->where('status', 'open')
            ->sum('margin_usd');

        // Closed/liquidated positions: margin is no longer locked (it's excluded above),
        // so only the realized P/L needs to be applied. Position::unrealizedPnl() already
        // floors losses at -margin_usd, so this can never push balance negative.
        $realizedFromClosedPositions = $this->positions()
            ->whereIn('status', ['closed', 'liquidated'])
            ->sum('realized_pnl');

        // Available cash = initial balance - spent + received - locked margin + realized P/L
        $availableCash = $initialBalance - $totalSpent + $totalReceived - $lockedMargin + $realizedFromClosedPositions;

        return max(0, round($availableCash, 2));
    }

    /**
     * Total account value in USD: available cash + current value of spot holdings
     * + current value (margin + unrealized P/L) of open leveraged positions.
     */
    public function getTotalPortfolioValue(): float
    {
        $total = $this->getCashBalance();

        $ownedByAsset = [];
        foreach ($this->trades as $trade) {
            $sign = $trade->type === 'buy' ? 1 : -1;
            $ownedByAsset[$trade->asset_id] = ($ownedByAsset[$trade->asset_id] ?? 0) + $sign * (float) $trade->amount;
        }

        foreach ($this->trades->pluck('asset')->unique('id') as $asset) {
            $owned = $ownedByAsset[$asset->id] ?? 0;
            if ($owned > 0 && $asset->price) {
                $total += $owned * (float) $asset->price;
            }
        }

        foreach ($this->positions()->where('status', 'open')->with('asset')->get() as $position) {
            $total += (float) $position->margin_usd;
            if ($position->asset->price) {
                $total += $position->unrealizedPnl((float) $position->asset->price);
            }
        }

        return max(0, round($total, 2));
    }
}
