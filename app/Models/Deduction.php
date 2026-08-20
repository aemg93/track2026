<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deduction extends Model
{
    protected $fillable = [
        'performance_id',
        'user_id',
        'category',
        'reason',
        'amount',
        'date',
        'is_installment',
        'installments',
        'installment_value',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'date' => 'datetime',
            'is_installment' => 'boolean',
            'installments' => 'integer',
            'installment_value' => 'float',
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