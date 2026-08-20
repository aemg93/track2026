<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ShiftStatus;
use App\Models\Performance;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShiftService
{
    /**
     * Inicia un nuevo turno para una Performance.
     */
    public function start(Performance $performance): Shift
    {
        return DB::transaction(function () use ($performance): Shift {

            $performance = Performance::query()
                ->lockForUpdate()
                ->findOrFail($performance->id);

            $hasActiveShift = Shift::query()
                ->where('performance_id', $performance->id)
                ->whereIn(
                    'status',
                    $this->activeStatuses()
                )
                ->exists();

            if ($hasActiveShift) {
                $this->throwShiftException(
                    'performance',
                    'La modelo ya tiene un turno activo.'
                );
            }

            $now = now();

            return Shift::create([
                'performance_id' =>
                    $performance->id,

                'studio_id' =>
                    $performance->studio_id,

                'started_at' =>
                    $now,

                'last_resumed_at' =>
                    $now,

                'paused_at' =>
                    null,

                'ended_at' =>
                    null,

                'worked_seconds' =>
                    0,

                'total_paused_seconds' =>
                    0,

                'status' =>
                    ShiftStatus::Active,
            ]);
        });
    }

    /**
     * Pausa un turno activo.
     *
     * El tiempo trabajado se acumula hasta
     * el momento exacto de la pausa.
     */
    public function pause(Shift $shift): Shift
    {
        return DB::transaction(function () use ($shift): Shift {

            $shift = Shift::query()
                ->lockForUpdate()
                ->findOrFail($shift->id);

            if (! $shift->isActive()) {
                $this->throwShiftException(
                    'shift',
                    'Solo un turno activo puede pausarse.'
                );
            }

            $now = now();

            $workedSeconds =
                (int) $shift->worked_seconds;

            if ($shift->last_resumed_at) {
                $workedSeconds +=
                    $shift->last_resumed_at
                        ->diffInSeconds($now);
            }

            $shift->update([
                'status' =>
                    ShiftStatus::Paused,

                'paused_at' =>
                    $now,

                'worked_seconds' =>
                    $workedSeconds,
            ]);

            return $shift->fresh(
                $this->shiftRelations()
            );
        });
    }

    /**
     * Reanuda un turno pausado.
     */
    public function resume(Shift $shift): Shift
    {
        return DB::transaction(function () use ($shift): Shift {

            $shift = Shift::query()
                ->lockForUpdate()
                ->findOrFail($shift->id);

            if (! $shift->isPaused()) {
                $this->throwShiftException(
                    'shift',
                    'Solo un turno pausado puede reanudarse.'
                );
            }

            $now = now();

            $pausedSeconds = 0;

            if ($shift->paused_at) {
                $pausedSeconds =
                    $shift->paused_at
                        ->diffInSeconds($now);
            }

            $shift->update([
                'status' =>
                    ShiftStatus::Active,

                'paused_at' =>
                    null,

                'last_resumed_at' =>
                    $now,

                'total_paused_seconds' =>
                    (int) $shift->total_paused_seconds
                    + $pausedSeconds,
            ]);

            return $shift->fresh(
                $this->shiftRelations()
            );
        });
    }

    /**
     * Finaliza un turno activo o pausado.
     *
     * El tiempo trabajado y pausado queda congelado
     * definitivamente.
     */
    public function finish(Shift $shift): Shift
    {
        return DB::transaction(function () use ($shift): Shift {

            $shift = Shift::query()
                ->lockForUpdate()
                ->findOrFail($shift->id);

            if ($shift->isFinished()) {
                return $shift->fresh(
                    $this->shiftRelations()
                );
            }

            if (
                ! $shift->isActive() &&
                ! $shift->isPaused()
            ) {
                $this->throwShiftException(
                    'shift',
                    'El turno no puede finalizarse.'
                );
            }

            $now = now();

            $workedSeconds =
                (int) $shift->worked_seconds;

            $pausedSeconds =
                (int) $shift->total_paused_seconds;

            /*
             * Si está activo, acumular el último
             * segmento trabajado.
             */
            if (
                $shift->isActive() &&
                $shift->last_resumed_at
            ) {
                $workedSeconds +=
                    $shift->last_resumed_at
                        ->diffInSeconds($now);
            }

            /*
             * Si está pausado, acumular el último
             * segmento de pausa.
             */
            if (
                $shift->isPaused() &&
                $shift->paused_at
            ) {
                $pausedSeconds +=
                    $shift->paused_at
                        ->diffInSeconds($now);
            }

            $shift->update([
                'status' =>
                    ShiftStatus::Finished,

                'ended_at' =>
                    $now,

                'paused_at' =>
                    null,

                'worked_seconds' =>
                    $workedSeconds,

                'total_paused_seconds' =>
                    $pausedSeconds,
            ]);

            return $shift->fresh(
                $this->shiftRelations()
            );
        });
    }

    /**
     * Obtiene el turno actual de una Performance.
     */
    public function current(
        Performance $performance
    ): ?Shift {
        return $performance
            ->shifts()
            ->whereIn(
                'status',
                $this->activeStatuses()
            )
            ->with($this->shiftRelations())
            ->latest('started_at')
            ->first();
    }

    /**
     * Verifica si la Performance tiene turno activo
     * o pausado.
     */
    public function hasActiveShift(
        Performance $performance
    ): bool {
        return $this->current($performance) !== null;
    }

    /**
     * Alias explícito para consumidores que trabajan
     * directamente con Performance.
     */
    public function activeByPerformance(
        Performance $performance
    ): ?Shift {
        return $this->current($performance);
    }

    /**
     * Obtiene todos los turnos activos/pausados.
     */
    public function active(): Collection
    {
        return Shift::query()
            ->whereHas('performance')
            ->whereIn(
                'status',
                $this->activeStatuses()
            )
            ->with($this->shiftRelations())
            ->orderBy('started_at')
            ->get();
    }

    /**
     * Obtiene los turnos activos de un Studio.
     */
    public function activeByStudioId(
        int $studioId
    ): Collection {
        return Shift::query()
            ->whereHas('performance')
            ->where(
                'studio_id',
                $studioId
            )
            ->whereIn(
                'status',
                $this->activeStatuses()
            )
            ->with($this->shiftRelations())
            ->orderBy('started_at')
            ->get();
    }

    /**
     * Retorna los minutos efectivamente trabajados.
     */
    public function workedMinutes(
        Shift $shift
    ): int {
        return $shift->workedMinutes();
    }

    /**
     * Estados que representan un turno actualmente abierto.
     */
    private function activeStatuses(): array
    {
        return [
            ShiftStatus::Active,
            ShiftStatus::Paused,
        ];
    }

    /**
     * Relaciones necesarias para el panel operativo.
     */
    private function shiftRelations(): array
    {
        return [
            'performance:id,studio_id,user_id,first_name,last_name,nickname',
            'performance.platforms:id,name,type,conversion_rate,multiplier',
            'studio:id,name',
        ];
    }

    /**
     * Error de validación consistente para las operaciones
     * de turno.
     */
    private function throwShiftException(
        string $field,
        string $message
    ): never {
        throw ValidationException::withMessages([
            $field => [$message],
        ]);
    }
}
