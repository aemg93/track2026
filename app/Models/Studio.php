<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Studio extends Model
{
    protected $fillable = [
        'name',
    ];


    public function performances(): HasMany
    {
        return $this->hasMany(Performance::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }
}