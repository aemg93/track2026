<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ShiftStatus;
use App\Models\Performance;
use App\Models\Shift;
use App\Models\Studio;
use Illuminate\Support\Collection;

class ShiftStatisticsService
{
    /**
     * Resumen general de estadísticas de turnos.
     */
    public function summary(): array
    {
        return [
            'today'      => $this->today(),
            'week'       => $this->week(),
            'month'      => $this->month(),
            'year'       => $this->year(),
            'historical' => $this->historical(),
            'activity'   => $this->activity(),
            'ranking'    => $this->ranking(),
            'averages'   => $this->averages(),
            // 'productivity' => $this->productivity(), ← opcional, lo dejamos para después
        ];
    }

    public function today(): array
    {
        return $this->period(now()->startOfDay());
    }

    public function week(): array
    {
        return $this->period(now()->startOfWeek());
    }

    public function month(): array
    {
        return $this->period(now()->startOfMonth());
    }

    public function year(): array
    {
        return $this->period(now()->startOfYear());
    }

    /**
     * Total histórico de horas trabajadas.
     */
    public function historical(): array
    {
        $seconds = Shift::query()
            ->where('status', ShiftStatus::Finished->value)
            ->sum('worked_seconds');

        return [
            'seconds' => $seconds,
            'minutes' => intdiv($seconds, 60),
            'hours'   => round($seconds / 3600, 2),
        ];
    }

    /**
     * Estadísticas por periodo.
     */
    private function period($start): array
    {
        $seconds = Shift::query()
            ->where('status', ShiftStatus::Finished->value)
            ->whereBetween('ended_at', [$start, now()])
            ->sum('worked_seconds');

        return [
            'seconds' => $seconds,
            'minutes' => intdiv($seconds, 60),
            'hours'   => round($seconds / 3600, 2),
        ];
    }

    /**
     * Estado actual de operaciones.
     */
    public function activity(): array
    {
        return [
            'active' => $this->activeShifts()->count(),
            'paused' => $this->pausedShifts()->count(),
            'active_models' => Shift::query()
                ->whereIn('status', [ShiftStatus::Active->value, ShiftStatus::Paused->value])
                ->distinct('performance_id')
                ->count('performance_id'),
            'finished' => Shift::query()
                ->where('status', ShiftStatus::Finished->value)
                ->count(),
        ];
    }

    /**
     * Ranking por horas trabajadas (top 10).
     */
    public function ranking(): Collection
    {
        return Shift::query()
            ->selectRaw('performance_id, SUM(worked_seconds) as total_seconds')
            ->where('status', ShiftStatus::Finished->value)
            ->groupBy('performance_id')
            ->orderByDesc('total_seconds')
            ->limit(10)
            ->with([
                'performance:id,first_name,last_name,nickname,studio_id',
                'performance.studio:id,name',
            ])
            ->get()
            ->map(fn(Shift $shift) => [
                'performance_id' => $shift->performance_id,
                'performance'    => $shift->performance,
                'seconds'        => (int) $shift->total_seconds,
                'hours'          => round($shift->total_seconds / 3600, 2),
            ]);
    }

    /**
     * Promedio de duración por turno.
     */
    public function averages(): array
    {
        $total = Shift::query()
            ->where('status', ShiftStatus::Finished->value)
            ->count();

        if ($total === 0) {
            return [
                'average_seconds' => 0,
                'average_hours'   => 0,
            ];
        }

        $seconds = Shift::query()
            ->where('status', ShiftStatus::Finished->value)
            ->sum('worked_seconds') / $total;

        return [
            'average_seconds' => (int) $seconds,
            'average_hours'   => round($seconds / 3600, 2),
        ];
    }

    /**
     * Horas acumuladas de una modelo.
     */
    public function hoursByPerformance(Performance $performance): array
    {
        $seconds = $performance->shifts()
            ->where('status', ShiftStatus::Finished->value)
            ->sum('worked_seconds');

        return [
            'seconds' => $seconds,
            'minutes' => intdiv($seconds, 60),
            'hours'   => round($seconds / 3600, 2),
        ];
    }

    /**
     * Horas acumuladas de un estudio.
     */
    public function hoursByStudio(Studio $studio): array
    {
        $seconds = Shift::query()
            ->where('studio_id', $studio->id)
            ->where('status', ShiftStatus::Finished->value)
            ->sum('worked_seconds');

        return [
            'seconds' => $seconds,
            'minutes' => intdiv($seconds, 60),
            'hours'   => round($seconds / 3600, 2),
        ];
    }

    /**
     * Turnos activos actuales.
     */
    public function activeShifts(): Collection
    {
        return Shift::query()
            ->where('status', ShiftStatus::Active->value)
            ->with(['performance', 'studio'])
            ->get();
    }

    /**
     * Turnos pausados actuales.
     */
    public function pausedShifts(): Collection
    {
        return Shift::query()
            ->where('status', ShiftStatus::Paused->value)
            ->with(['performance', 'studio'])
            ->get();
    }

    // Opcional: productividad
    // public function productivity(): array
    // {
    //     // Aquí definirías cómo medir productividad (ej. horas activas vs pausadas)
    // }
}
