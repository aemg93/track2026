<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Performance;

class Platform extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',

        'multiplier',
        'conversion_rate',

        'is_active',
        'logo',
        'description',
    ];

    protected $casts = [
        'multiplier' => 'float',
        'conversion_rate' => 'float',
        'is_active' => 'boolean',
    ];

    public function performances()
    {
        return $this->belongsToMany(
            Performance::class,
            'performance_platform',
            'platform_id',
            'performance_id'
        )
        ->withPivot([
            'hours_streamed',
            'tokens',
            'earnings_usd',

            'multiplier',
            'conversion_rate',

            'ranking_score',
            'recorded_at',
        ])
        ->withTimestamps();
    }
}