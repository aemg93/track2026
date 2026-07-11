<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAUSED = 'paused';

    public const STATUS_FINISHED = 'finished';


    protected $fillable = [
        'performance_id',
        'studio_id',
        'started_at',
        'ended_at',
        'status',
    ];


    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];


    public function performance(): BelongsTo
    {
        return $this->belongsTo(Performance::class);
    }


    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }


    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }


    public function isPaused(): bool
    {
        return $this->status === self::STATUS_PAUSED;
    }


    public function isFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }


    public function durationMinutes(): int
    {
        if (! $this->started_at) {
            return 0;
        }

        return $this->started_at
            ->diffInMinutes(
                $this->ended_at ?? now()
            );
    }
}