<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'content',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Users who liked this post.
     */
    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes')->withTimestamps();
    }

    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->likers()->where('users.id', $user->id)->exists();
    }

    /**
     * Extract $CASHTAGS from the post content, e.g. "$BTC" -> "BTC".
     *
     * @return array<int, string>
     */
    public function cashtags(): array
    {
        preg_match_all('/\$([A-Za-z]{2,10})\b/', $this->content, $matches);

        return array_values(array_unique(array_map('strtoupper', $matches[1] ?? [])));
    }

    /**
     * Render the post content as HTML with $CASHTAGS linked to their asset page.
     * Content is escaped first so this is safe against XSS.
     */
    public function renderedContent(): string
    {
        $escaped = e($this->content);

        return preg_replace_callback('/\$([A-Za-z]{2,10})\b/', function ($match) {
            $symbol = strtoupper($match[1]);
            $asset = Asset::where('symbol', $symbol)->first();

            if (!$asset) {
                return $match[0];
            }

            $url = route('assets.show', $asset);
            return '<a href="' . $url . '" class="cashtag">$' . $symbol . '</a>';
        }, $escaped);
    }
}
