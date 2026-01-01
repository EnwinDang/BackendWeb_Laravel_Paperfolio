<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    protected $fillable = [
        'name',
        'order',
    ];

    public function items()
    {
        return $this->hasMany(FaqItem::class)->orderBy('order');
    }
}
