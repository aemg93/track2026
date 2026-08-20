<?php

declare(strict_types=1);

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

            'total_paused_seconds' => 'integer',
            'worked_seconds' => 'integer',

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
        return $this->belongsTo(
            Performance::class
        );
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(
            Studio::class
        );
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
    | Elapsed Time
    |--------------------------------------------------------------------------
    */

    public function elapsedSeconds(
        ?CarbonInterface $at = null
    ): int {
        if (! $this->started_at) {
            return 0;
        }

        $reference = $at
            ?? $this->ended_at
            ?? now();

        return max(
            0,
            (int) $this->started_at->diffInSeconds(
                $reference
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Worked Time
    |--------------------------------------------------------------------------
    */

    public function workedSeconds(
        ?CarbonInterface $at = null
    ): int {
        /*
         * Un turno finalizado ya tiene su tiempo
         * consolidado en worked_seconds.
         */
        if ($this->isFinished()) {
            return max(
                0,
                (int) $this->worked_seconds
            );
        }

        /*
         * Mientras está pausado no se acumula
         * tiempo adicional.
         */
        if ($this->isPaused()) {
            return max(
                0,
                (int) $this->worked_seconds
            );
        }

        /*
         * Turno activo:
         *
         * worked_seconds contiene todo el tiempo
         * trabajado de períodos anteriores.
         *
         * last_resumed_at marca el inicio del
         * período actualmente activo.
         */
        $seconds = (int) $this->worked_seconds;

        if ($this->isActive() && $this->last_resumed_at) {
            $reference = $at ?? now();

            $seconds += (int) $this->last_resumed_at
                ->diffInSeconds($reference);
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
        return $this->formatSeconds(
            $this->workedSeconds($at)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Studio Time
    |--------------------------------------------------------------------------
    */

    public function studioTime(
        ?CarbonInterface $at = null
    ): string {
        return $this->formatSeconds(
            $this->elapsedSeconds($at)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Formatting
    |--------------------------------------------------------------------------
    */

    private function formatSeconds(
        int $seconds
    ): string {
        $seconds = max(0, $seconds);

        return sprintf(
            '%02d:%02d:%02d',
            intdiv($seconds, 3600),
            intdiv(
                $seconds % 3600,
                60
            ),
            $seconds % 60
        );
    }
}
