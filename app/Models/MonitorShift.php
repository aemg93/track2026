<?php

namespace App\Models;
use App\Enums\MonitorShiftStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonitorShift extends Model
{
    protected $fillable = [
        'monitor_id',
        'studio_id',
        'started_at',
        'ended_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'status' => MonitorShiftStatus::class,
    ];

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'monitor_id'
        );
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(
            Studio::class
        );
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(
            Earning::class
        );
    }

    public function isActive(): bool
    {
        return $this->status === MonitorShiftStatus::Active;
    }


    public function isFinished(): bool
    {
        return $this->status === MonitorShiftStatus::Finished;
    }


    public function durationSeconds(): int
    {
        if (! $this->started_at) {
            return 0;
        }


        return $this->started_at
            ->diffInSeconds(
                $this->ended_at ?? now()
            );
    }


    public function durationMinutes(): int
    {
        return (int) floor(
            $this->durationSeconds() / 60
        );
    }
}