<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penalty extends Model
{
    protected $fillable = [
        'performance_id',
        'user_id',
        'reason',
        'amount',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'date' => 'datetime',
        ];
    }

    public function performance(): BelongsTo
    {
        return $this->belongsTo(
            Performance::class
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }
}