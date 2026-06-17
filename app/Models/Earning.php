<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    protected $fillable = [

        'performance_id',

        'period_start',
        'period_end',

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

        'period_start' => 'date',
        'period_end'   => 'date',
        'paid_at'      => 'datetime',

        'gross_usd'         => 'decimal:2',
        'bonus_usd'         => 'decimal:2',
        'penalty_usd'       => 'decimal:2',
        'deduction_usd'     => 'decimal:2',
        'net_usd'           => 'decimal:2',

        'model_percentage'  => 'decimal:2',
        'studio_percentage' => 'decimal:2',

        'model_share_usd'   => 'decimal:2',
        'studio_share_usd'  => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function performance()
    {
        return $this->belongsTo(Performance::class);
    }
}