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

    /**
     * Tiempo total transcurrido desde que inició el turno.
     * Este tiempo NUNCA se pausa.
     */
    public function elapsedSeconds(
        ?CarbonInterface $at = null
    ): int {

        if (! $this->started_at) {
            return 0;
        }

        $reference = $at;

        if (! $reference) {
            $reference = $this->ended_at ?? now();
        }

        return (int) $this->started_at->diffInSeconds($reference);
    }

    /**
     * Tiempo efectivo de transmisión.
     *
     * Utiliza la arquitectura por snapshots:
     *
     * worked_seconds
     * +
     * (now - last_resumed_at)
     */
    public function workedSeconds(
        ?CarbonInterface $at = null
    ): int {

        if ($this->isFinished()) {
            return (int) $this->worked_seconds;
        }

        if ($this->isPaused()) {
            return (int) $this->worked_seconds;
        }

        $seconds = (int) $this->worked_seconds;

        if ($this->last_resumed_at) {

            $reference = $at ?? now();

            $seconds += $this->last_resumed_at->diffInSeconds($reference);
        }

        return max(0, $seconds);
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

    /**
     * Tiempo total dentro del estudio.
     * No se detiene cuando el turno está pausado.
     */
    public function studioTime(
        ?CarbonInterface $at = null
    ): string {

        $seconds = $this->elapsedSeconds($at);

        return sprintf(
            '%02d:%02d:%02d',
            intdiv($seconds, 3600),
            intdiv($seconds % 3600, 60),
            $seconds % 60
        );
    }
}