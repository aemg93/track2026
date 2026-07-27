<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Earning extends Model
{
    protected $fillable = [
        'performance_id',
        'platform_id',
        'user_id',
        'monitor_shift_id',

        'earned_at',

        'original_amount',
        'original_currency',
        'real_tokens',

        'conversion_rate',
        'multiplier',

        'gross_usd',

        'bonus_usd',
        'penalty_usd',
        'deduction_usd',

        'net_usd',

        'model_percentage',
        'studio_percentage',

        'model_share_usd',
        'studio_share_usd',

        'status',
        'paid_at',
    ];


    protected $casts = [
        'earned_at' => 'datetime',
        'paid_at' => 'datetime',

        'original_amount' => 'decimal:2',
        'real_tokens' => 'decimal:2',

        'conversion_rate' => 'decimal:6',
        'multiplier' => 'decimal:4',

        'gross_usd' => 'decimal:2',

        'bonus_usd' => 'decimal:2',
        'penalty_usd' => 'decimal:2',
        'deduction_usd' => 'decimal:2',

        'net_usd' => 'decimal:2',

        'model_percentage' => 'decimal:2',
        'studio_percentage' => 'decimal:2',

        'model_share_usd' => 'decimal:2',
        'studio_share_usd' => 'decimal:2',
    ];

    public function performance(): BelongsTo
    {
        return $this->belongsTo(
            Performance::class
        );
    }


    public function platform(): BelongsTo
    {
        return $this->belongsTo(
            Platform::class
        );
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function monitorShift(): BelongsTo
    {
        return $this->belongsTo(
            MonitorShift::class
        );
    }
}