<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        'coingecko_id',
        'price',
        'price_last_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:8',
            'price_last_updated_at' => 'datetime',
        ];
    }

    public function trades()
    {
        return $this->hasMany(Trade::class);
    }
}
