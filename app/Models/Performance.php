<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Studio;
use App\Models\User;
use App\Models\Earning;
use App\Models\Bonus;
use App\Models\Penalty;
use App\Models\Deduction;
use App\Models\Sale;
use App\Models\PerformanceSplit;
use App\Models\Platform;

class Performance extends Model
{
    protected $fillable = [
        'studio_id',
        'user_id',

        'first_name',
        'last_name',
        'nickname',

        'email',
        'phone',

        'country',
        'city',
        'address',

        'document_type',
        'document_number',

        'birth_date',
        'profile_photo',

        'active',
        'hours_streamed',
        'ranking_score',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function earnings()
    {
        return $this->hasMany(Earning::class);
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class);
    }

    public function penalties()
    {
        return $this->hasMany(Penalty::class);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function split()
    {
        return $this->hasOne(PerformanceSplit::class);
    }

    /*
    |--------------------------------------------------------------------------
    | PLATFORMS (CORREGIDO)
    |--------------------------------------------------------------------------
    */

    public function platforms()
    {
        return $this->belongsToMany(
            Platform::class,
            'performance_platform',
            'performance_id',
            'platform_id'
        )
        ->withPivot([
            'hours_streamed',
            'earnings_usd',
            'tokens',
            'multiplier',
            'conversion_rate',
            'recorded_at',
        ])
        ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getNameAttribute()
    {
        return trim(
            ($this->first_name ?? '') . ' ' . ($this->last_name ?? '')
        ) ?: ($this->nickname ?? 'Sin nombre');
    }
}