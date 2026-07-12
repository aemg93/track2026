<?php

namespace App\Services;

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

            if ($this->hasActiveShift($performance)) {
                throw ValidationException::withMessages([
                    'performance' => [
                        'La modelo ya tiene un turno activo.',
                    ],
                ]);
            }

            return Shift::create([
                'performance_id' => $performance->id,
                'studio_id'      => $performance->studio_id,
                'started_at'     => now(),
                'status'         => Shift::STATUS_ACTIVE,
            ]);
        });
    }

    public function pause(Shift $shift): Shift
    {
        if (! $shift->isActive()) {
            throw ValidationException::withMessages([
                'shift' => [
                    'Solo un turno activo puede pausarse.',
                ],
            ]);
        }

        $shift->update([
            'status' => Shift::STATUS_PAUSED,
        ]);

        return $shift->fresh();
    }

    public function resume(Shift $shift): Shift
    {
        if (! $shift->isPaused()) {
            throw ValidationException::withMessages([
                'shift' => [
                    'Solo un turno pausado puede reanudarse.',
                ],
            ]);
        }

        $shift->update([
            'status' => Shift::STATUS_ACTIVE,
        ]);

        return $shift->fresh();
    }

    public function finish(Shift $shift): Shift
    {
        if ($shift->isFinished()) {
            return $shift;
        }

        if (! in_array($shift->status, [
            Shift::STATUS_ACTIVE,
            Shift::STATUS_PAUSED,
        ], true)) {
            throw ValidationException::withMessages([
                'shift' => [
                    'El turno no puede finalizarse.',
                ],
            ]);
        }

        $shift->update([
            'status'   => Shift::STATUS_FINISHED,
            'ended_at' => now(),
        ]);

        return $shift->fresh();
    }

    public function current(Performance $performance): ?Shift
    {
        return $performance->shifts()
            ->whereIn('status', [
                Shift::STATUS_ACTIVE,
                Shift::STATUS_PAUSED,
            ])
            ->latest('started_at')
            ->first();
    }

    public function active(): Collection
    {
        return Shift::query()
            ->with([
                'performance',
                'studio',
            ])
            ->whereIn('status', [
                Shift::STATUS_ACTIVE,
                Shift::STATUS_PAUSED,
            ])
            ->orderBy('started_at')
            ->get();
    }

    public function activeByStudio(Studio $studio): Collection
    {
        return Shift::query()
            ->with([
                'performance',
                'studio',
            ])
            ->where('studio_id', $studio->id)
            ->whereIn('status', [
                Shift::STATUS_ACTIVE,
                Shift::STATUS_PAUSED,
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

    public function durationMinutes(Shift $shift): int
    {
        if (! $shift->started_at) {
            return 0;
        }

        return $shift->started_at->diffInMinutes(
            $shift->ended_at ?? now()
        );
    }
}