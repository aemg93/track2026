<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ShiftStatus;
use App\Models\Performance;
use App\Models\Shift;
use App\Models\Studio;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShiftService
{
    public function start(Performance $performance): Shift
    {
        return DB::transaction(function () use ($performance) {

            $performance = Performance::query()
                ->lockForUpdate()
                ->findOrFail($performance->id);


            if (
                Shift::query()
                    ->where('performance_id', $performance->id)
                    ->whereIn('status', [
                        ShiftStatus::Active,
                        ShiftStatus::Paused,
                    ])
                    ->exists()
            ) {
                $this->throwShiftException(
                    'performance',
                    'La modelo ya tiene un turno activo.'
                );
            }


            return Shift::create([
                'performance_id'       => $performance->id,
                'studio_id'            => $performance->studio_id,
                'started_at'           => now(),
                'last_resumed_at'      => now(),
                'paused_at'            => null,
                'ended_at'             => null,
                'worked_seconds'       => 0,
                'total_paused_seconds' => 0,
                'status'               => ShiftStatus::Active,
            ]);
        });
    }


    public function pause(Shift $shift): Shift
    {
        return DB::transaction(function () use ($shift) {

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

            $workedSeconds = $shift->worked_seconds;


            if ($shift->last_resumed_at) {
                $workedSeconds +=
                    $shift->last_resumed_at
                        ->diffInSeconds($now);
            }


            $shift->update([
                'status' => ShiftStatus::Paused,
                'paused_at' => $now,
                'worked_seconds' => $workedSeconds,
            ]);


            return $shift->fresh();
        });
    }


    public function resume(Shift $shift): Shift
    {
        return DB::transaction(function () use ($shift) {

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


            $pausedSeconds = $shift->paused_at
                ? $shift->paused_at->diffInSeconds($now)
                : 0;


            $shift->update([
                'status' => ShiftStatus::Active,
                'paused_at' => null,
                'last_resumed_at' => $now,
                'total_paused_seconds' =>
                    $shift->total_paused_seconds + $pausedSeconds,
            ]);


            return $shift->fresh();
        });
    }


    public function finish(Shift $shift): Shift
    {
        return DB::transaction(function () use ($shift) {

            $shift = Shift::query()
                ->lockForUpdate()
                ->findOrFail($shift->id);


            if ($shift->isFinished()) {
                return $shift;
            }


            if (! $shift->isActive() && ! $shift->isPaused()) {

                $this->throwShiftException(
                    'shift',
                    'El turno no puede finalizarse.'
                );
            }


            $now = now();


            $pausedSeconds =
                $shift->total_paused_seconds;


            if ($shift->isPaused() && $shift->paused_at) {
                $pausedSeconds +=
                    $shift->paused_at
                        ->diffInSeconds($now);
            }


            $workedSeconds =
                $shift->worked_seconds;


            if ($shift->isActive() && $shift->last_resumed_at) {
                $workedSeconds +=
                    $shift->last_resumed_at
                        ->diffInSeconds($now);
            }


            $shift->update([
                'status' => ShiftStatus::Finished,
                'ended_at' => $now,
                'paused_at' => null,
                'worked_seconds' => $workedSeconds,
                'total_paused_seconds' => $pausedSeconds,
            ]);


            return $shift->fresh();
        });
    }


    public function current(Performance $performance): ?Shift
    {
        return $performance->shifts()
            ->whereIn('status', [
                ShiftStatus::Active,
                ShiftStatus::Paused,
            ])
            ->with([
                'performance.user',
                'performance.platforms',
                'studio',
            ])
            ->latest('started_at')
            ->first();
    }


    public function active(): Collection
    {
        return Shift::query()
            ->whereHas('performance')
            ->whereIn('status', [
                ShiftStatus::Active,
                ShiftStatus::Paused,
            ])
            ->with([
                'performance.user',
                'performance.platforms',
                'studio',
            ])
            ->orderBy('started_at')
            ->get();
    }


    public function activeByStudio(Studio $studio): Collection
    {
        return Shift::query()
            ->whereHas('performance')
            ->where('studio_id', $studio->id)
            ->whereIn('status', [
                ShiftStatus::Active,
                ShiftStatus::Paused,
            ])
            ->with([
                'performance.user',
                'performance.platforms',
                'studio',
            ])
            ->orderBy('started_at')
            ->get();
    }


    public function activeByPerformance(Performance $performance): ?Shift
    {
        return $this->current($performance);
    }


    public function hasActiveShift(Performance $performance): bool
    {
        return $this->current($performance) !== null;
    }


    public function workedMinutes(Shift $shift): int
    {
        return $shift->workedMinutes();
    }


    private function throwShiftException(
        string $field,
        string $message
    ): never {
        throw ValidationException::withMessages([
            $field => [$message],
        ]);
    }
}