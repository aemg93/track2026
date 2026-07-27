<?php

namespace App\Services;

use App\Enums\MonitorShiftStatus;
use App\Models\MonitorShift;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MonitorShiftService
{
    /**
     * Inicia el turno operativo del monitor.
     */
    public function start(
        User $monitor,
        int $studioId
    ): MonitorShift {

        return DB::transaction(function () use (
            $monitor,
            $studioId
        ) {

            $exists = MonitorShift::query()
                ->where('studio_id', $studioId)
                ->where('status', MonitorShiftStatus::Active)
                ->exists();


            if ($exists) {
                throw ValidationException::withMessages([
                    'studio' => 'El estudio ya tiene un monitor activo.',
                ]);
            }


            return MonitorShift::create([

                'monitor_id' => $monitor->id,

                'studio_id' => $studioId,

                'started_at' => now(),

                'ended_at' => null,

                'status' => MonitorShiftStatus::Active,

            ]);
        });
    }


    /**
     * Finaliza únicamente el turno del monitor.
     *
     * No modifica shifts de modelos.
     */
    public function finish(
        MonitorShift $monitorShift
    ): MonitorShift {

        return DB::transaction(function () use (
            $monitorShift
        ) {

            $monitorShift = MonitorShift::query()
                ->lockForUpdate()
                ->findOrFail($monitorShift->id);


            if (
                $monitorShift->status === MonitorShiftStatus::Finished
            ) {
                return $monitorShift;
            }


            $monitorShift->update([

                'ended_at' => now(),

                'status' => MonitorShiftStatus::Finished,

            ]);


            return $monitorShift->fresh([
                'monitor',
                'studio',
            ]);

        });
    }


    /**
     * Turno activo del monitor.
     */
    public function activeByMonitor(
        User $monitor
    ): ?MonitorShift {

        return MonitorShift::query()

            ->where('monitor_id', $monitor->id)

            ->where('status', MonitorShiftStatus::Active)

            ->with([
                'studio',
            ])

            ->latest('started_at')

            ->first();
    }


    /**
     * Monitores activos del estudio.
     */
    public function activeByStudio(
        int $studioId
    ) {

        return MonitorShift::query()

            ->where('studio_id', $studioId)

            ->where('status', MonitorShiftStatus::Active)

            ->with([
                'monitor',
            ])

            ->orderBy('started_at')

            ->get();
    }
}