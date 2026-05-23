<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    protected $fillable=[
        'name'
    ];

    public function performances()
    {
        return $this->hasMany(Performance::class);
    }
}