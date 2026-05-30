<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    protected $casts = [
        'amount' => 'float',
        'installment_value' => 'float',
        'is_installment' => 'boolean',
        'date' => 'date',
    ];

    public function performance()
    {
        return $this->belongsTo(Performance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}