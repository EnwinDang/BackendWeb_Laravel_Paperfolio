<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
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
}
