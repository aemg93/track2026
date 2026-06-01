<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    | ACCESSOR STRIPE STYLE (NOMBRE UNIFICADO)
    |--------------------------------------------------------------------------
    */

    public function getNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return trim($this->first_name . ' ' . $this->last_name);
        }

        return $this->nickname ?? 'Sin nombre';
    }
}