<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Position extends Model
{
    protected $fillable = [
        'user_id',
        'asset_id',
        'direction',
        'leverage',
        'margin_usd',
        'entry_price',
        'close_price',
        'status',
        'realized_pnl',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'margin_usd' => 'decimal:8',
            'entry_price' => 'decimal:8',
            'close_price' => 'decimal:8',
            'realized_pnl' => 'decimal:8',
            'closed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Notional size of the position (margin * leverage), in USD.
     */
    public function notional(): float
    {
        return (float) $this->margin_usd * $this->leverage;
    }

    /**
     * Units of the underlying asset this position controls.
     */
    public function units(): float
    {
        $entryPrice = (float) $this->entry_price;
        return $entryPrice > 0 ? $this->notional() / $entryPrice : 0;
    }

    /**
     * Simplified unrealized P/L at a given mark price.
     * Losses are floored at losing the full margin (never goes below -margin_usd),
     * so the account can never go negative from a single position.
     */
    public function unrealizedPnl(float $markPrice): float
    {
        $units = $this->units();
        $priceDiff = $this->direction === 'long'
            ? $markPrice - (float) $this->entry_price
            : (float) $this->entry_price - $markPrice;

        $pnl = $priceDiff * $units;

        return max($pnl, -1 * (float) $this->margin_usd);
    }

    public function roePercent(float $markPrice): float
    {
        $margin = (float) $this->margin_usd;
        return $margin > 0 ? ($this->unrealizedPnl($markPrice) / $margin) * 100 : 0;
    }

    /**
     * A position is liquidated once losses wipe out the full margin.
     */
    public function isLiquidatable(float $markPrice): bool
    {
        return $this->unrealizedPnl($markPrice) <= -1 * (float) $this->margin_usd;
    }
}
