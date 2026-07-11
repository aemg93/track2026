<?php

namespace App\Services;

use App\Models\Performance;
use App\Models\Shift;
use App\Models\Studio;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class ShiftService
{
    
    public function start(Performance $performance): Shift
    {
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
            'status'         => 'active',
        ]);
    }

    public function pause(Shift $shift): Shift
    {
        if ($shift->status !== 'active') {
            throw ValidationException::withMessages([
                'shift' => [
                    'Solo un turno activo puede pausarse.',
                ],
            ]);
        }

        $shift->update([
            'status' => 'paused',
        ]);

        return $shift->fresh();
    }

    public function resume(Shift $shift): Shift
    {
        if ($shift->status !== 'paused') {
            throw ValidationException::withMessages([
                'shift' => [
                    'Solo un turno pausado puede reanudarse.',
                ],
            ]);
        }

        $shift->update([
            'status' => 'active',
        ]);

        return $shift->fresh();
    }

    public function finish(Shift $shift): Shift
    {
        if ($shift->status === 'finished') {
            return $shift;
        }

        if (! in_array($shift->status, [
            'active',
            'paused',
        ])) {
            throw ValidationException::withMessages([
                'shift' => [
                    'El turno no puede finalizarse.',
                ],
            ]);
        }

        $shift->update([
            'status'   => 'finished',
            'ended_at' => now(),
        ]);

        return $shift->fresh();
    }

    public function current(Performance $performance): ?Shift
    {
        return $performance->shifts()
            ->whereIn('status', [
                'active',
                'paused',
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
                'active',
                'paused',
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
                'active',
                'paused',
            ])
            ->orderBy('started_at')
            ->get();
    }

    public function activeByPerformance(
        Performance $performance
    ): ?Shift {
        return $this->current($performance);
    }

    public function hasActiveShift(
        Performance $performance
    ): bool {
        return $this->current($performance) !== null;
    }

    public function durationMinutes(Shift $shift): int
    {
        if (! $shift->started_at) {
            return 0;
        }

        $end = $shift->ended_at ?? now();

        return $shift->started_at
            ->diffInMinutes($end);
    }
}