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
     *
     * Un estudio solo puede tener un monitor activo
     * al mismo tiempo.
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
                ->where(
                    'status',
                    MonitorShiftStatus::Active
                )
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
     * IMPORTANTE:
     * No modifica los turnos de las modelos.
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
                $monitorShift->status ===
                MonitorShiftStatus::Finished
            ) {
                return $monitorShift->load([
                    'monitor',
                    'studio',
                ]);
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
     * Obtiene el turno activo del monitor.
     */
    public function activeByMonitor(
        User $monitor
    ): ?MonitorShift {

        return MonitorShift::query()
            ->where(
                'monitor_id',
                $monitor->id
            )
            ->where(
                'status',
                MonitorShiftStatus::Active
            )
            ->with([
                'studio',
            ])
            ->latest('started_at')
            ->first();
    }


    /**
     * Obtiene los turnos activos de monitor
     * pertenecientes a un estudio.
     */
    public function activeByStudio(
        int $studioId
    ) {

        return MonitorShift::query()
            ->where(
                'studio_id',
                $studioId
            )
            ->where(
                'status',
                MonitorShiftStatus::Active
            )
            ->with([
                'monitor',
                'studio',
            ])
            ->orderBy('started_at')
            ->get();
    }
}
