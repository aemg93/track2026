<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable=[
        'product_id',
        'performance_id',
        'amount'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function performance()
    {
        return $this->belongsTo(Performance::class);
    }
}