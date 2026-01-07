<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'read',
        'admin_response',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'read' => 'boolean',
            'responded_at' => 'datetime',
        ];
    }
}
