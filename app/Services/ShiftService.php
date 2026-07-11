<?php

namespace App\Services;

use App\Models\Performance;
use App\Models\Shift;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class ShiftService
{
    public function start(Performance $performance): Shift
    {
        if ($this->current($performance)) {
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
            return $shift;
        }

        $shift->update([
            'status' => 'paused',
        ]);

        return $shift->fresh();
    }

    public function resume(Shift $shift): Shift
    {
        if ($shift->status !== 'paused') {
            return $shift;
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

        $shift->update([
            'status'    => 'finished',
            'ended_at'  => now(),
        ]);

        return $shift->fresh();
    }

    public function current(Performance $performance): ?Shift
    {
        return $performance->shifts()
            ->whereIn('status', ['active', 'paused'])
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

    public function hasActiveShift(Performance $performance): bool
    {
        return $this->current($performance) !== null;
    }
}