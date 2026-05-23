<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Performance extends Model
{
    protected $fillable = [

        'studio_id',

        'user_id',

        'name',

        'active',

        'hours_streamed',

        'ranking_score'

    ];

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

    // Configuración financiera del modelo
    public function split()
    {
        return $this->hasOne(PerformanceSplit::class);
    }
}