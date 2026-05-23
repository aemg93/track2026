<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected $fillable=[
        'performance_id',
        'user_id',
        'reason',
        'amount',
        'date'
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