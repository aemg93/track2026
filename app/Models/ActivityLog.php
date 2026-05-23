<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [

        'performance_id',
        'platform_id',
        'user_id',
        'tokens',
        'date'

    ];

    public function performance()
    {
        return $this->belongsTo(Performance::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}