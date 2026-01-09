<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class News extends Model
{
    protected $fillable = [
        'title',
        'excerpt',
        'image',
        'content',
        'publication_date',
    ];

    protected function casts(): array
    {
        return [
            'publication_date' => 'datetime',
        ];
    }

    public function comments(): HasMany
    {
        return $this->hasMany(NewsComment::class)->orderBy('created_at', 'asc');
    }
}
