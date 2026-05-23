<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceSplit extends Model
{
    protected $fillable = [

        'performance_id',

        'model_percentage',

        'studio_percentage'

    ];

    public function performance()
    {
        return $this->belongsTo(Performance::class);
    }
}