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
        'username',
        'date_of_birth',
        'profile_picture',
        'about_me',
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
            'date_of_birth' => 'date',
        ];
    }

    public function trades()
    {
        return $this->hasMany(Trade::class);
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
        
        // Available cash = initial balance - spent + received
        $availableCash = $initialBalance - $totalSpent + $totalReceived;
        
        return max(0, round($availableCash, 2));
    }
}
