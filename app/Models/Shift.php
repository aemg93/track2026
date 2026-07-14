<?php

namespace App\Models;

use App\Enums\ShiftStatus;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    protected $fillable = [
        'performance_id',
        'studio_id',

        'started_at',
        'last_resumed_at',
        'paused_at',
        'ended_at',

        'total_paused_seconds',
        'worked_seconds',

        'status',
    ];


    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'last_resumed_at' => 'datetime',
            'paused_at' => 'datetime',
            'ended_at' => 'datetime',

            'status' => ShiftStatus::class,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function performance(): BelongsTo
    {
        return $this->belongsTo(Performance::class);
    }


    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === ShiftStatus::Active;
    }


    public function isPaused(): bool
    {
        return $this->status === ShiftStatus::Paused;
    }


    public function isFinished(): bool
    {
        return $this->status === ShiftStatus::Finished;
    }


    /*
    |--------------------------------------------------------------------------
    | Time
    |--------------------------------------------------------------------------
    */

    protected function referenceTime(
        ?CarbonInterface $at = null
    ): CarbonInterface {

        if ($at) {
            return $at;
        }


        if ($this->ended_at) {
            return $this->ended_at;
        }


        if ($this->isPaused() && $this->paused_at) {
            return $this->paused_at;
        }


        return now();
    }


    public function elapsedSeconds(
        ?CarbonInterface $at = null
    ): int {

        if (! $this->started_at) {
            return 0;
        }


        return (int) $this->started_at->diffInSeconds(
            $this->referenceTime($at)
        );
    }


    public function workedSeconds(
        ?CarbonInterface $at = null
    ): int {

        /*
        |--------------------------------------------------------------------------
        | Turno finalizado
        |--------------------------------------------------------------------------
        */

        if ($this->isFinished()) {
            return (int) $this->worked_seconds;
        }


        $pausedSeconds = (int) $this->total_paused_seconds;


        /*
        |--------------------------------------------------------------------------
        | Pausa actual en curso
        |--------------------------------------------------------------------------
        */

        if (
            $this->isPaused()
            &&
            $this->paused_at
        ) {

            $pausedSeconds += (int) $this->paused_at->diffInSeconds(
                $this->referenceTime($at)
            );
        }


        return (int) max(
            0,
            $this->elapsedSeconds($at)
            -
            $pausedSeconds
        );
    }


    public function workedMinutes(
        ?CarbonInterface $at = null
    ): int {

        return intdiv(
            $this->workedSeconds($at),
            60
        );
    }


    public function workedHours(
        ?CarbonInterface $at = null
    ): float {

        return round(
            $this->workedSeconds($at) / 3600,
            2
        );
    }


    public function workedTime(
        ?CarbonInterface $at = null
    ): string {

        $seconds = $this->workedSeconds($at);


        return sprintf(
            '%02d:%02d:%02d',
            intdiv($seconds, 3600),
            intdiv($seconds % 3600, 60),
            $seconds % 60
        );
    }
}