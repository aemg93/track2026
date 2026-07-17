<?php

namespace App\Services;

use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ShiftActivityService
{
    public function activity(Shift $shift): array
    {
        [$from, $to] = $this->range($shift);

        $data = [
            'earnings' => $this->earnings($shift, $from, $to),
            'bonuses' => $this->bonuses($shift, $from, $to),
            'penalties' => $this->penalties($shift, $from, $to),
            'deductions' => $this->deductions($shift, $from, $to),
        ];

        return [
            'timeline' => $this->buildTimeline($data),
            'summary' => $this->buildSummary($data),
        ];
    }

    public function timeline(Shift $shift): array
    {
        return $this->activity($shift)['timeline'];
    }

    public function summary(Shift $shift): array
    {
        return $this->activity($shift)['summary'];
    }

    private function range(Shift $shift): array
    {
        return [
            $shift->started_at,
            $shift->ended_at ?? now(),
        ];
    }

    private function buildTimeline(array $data): array
    {
        $timeline = collect();

        foreach ($data['earnings'] as $earning) {
            $timeline->push([
                'id' => $earning->id,
                'type' => 'earning',
                'title' => 'Ganancia',
                'description' => optional($earning->platform)->name,
                'performed_by' => null,
                'amount' => (float) $earning->gross_usd,
                'date' => $earning->earned_at,
            ]);
        }

        foreach ($data['bonuses'] as $bonus) {
            $timeline->push([
                'id' => $bonus->id,
                'type' => 'bonus',
                'title' => 'Bono',
                'description' => $bonus->reason,
                'performed_by' => optional($bonus->user)->name,
                'amount' => (float) $bonus->amount,
                'date' => $bonus->created_at,
            ]);
        }

        foreach ($data['penalties'] as $penalty) {
            $timeline->push([
                'id' => $penalty->id,
                'type' => 'penalty',
                'title' => 'Penalización',
                'description' => $penalty->reason,
                'performed_by' => optional($penalty->user)->name,
                'amount' => (float) $penalty->amount,
                'date' => $penalty->created_at,
            ]);
        }

        foreach ($data['deductions'] as $deduction) {
            $timeline->push([
                'id' => $deduction->id,
                'type' => 'deduction',
                'title' => 'Descuento',
                'description' => $deduction->reason,
                'performed_by' => optional($deduction->user)->name,
                'amount' => (float) $deduction->amount,
                'date' => $deduction->created_at,
            ]);
        }

        return $timeline
            ->sortByDesc('date')
            ->values()
            ->toArray();
    }

    private function buildSummary(array $data): array
    {
        $earnings = $data['earnings']->sum('gross_usd');
        $bonuses = $data['bonuses']->sum('amount');
        $penalties = $data['penalties']->sum('amount');
        $deductions = $data['deductions']->sum('amount');

        return [
            'earnings' => round($earnings, 2),
            'bonuses' => round($bonuses, 2),
            'penalties' => round($penalties, 2),
            'deductions' => round($deductions, 2),
            'net' => round(
                $earnings
                + $bonuses
                - $penalties
                - $deductions,
                2
            ),
        ];
    }

    private function earnings(
        Shift $shift,
        Carbon $from,
        Carbon $to
    ): Collection {
        if (! $shift->performance) {
            return collect();
        }

        return $shift->performance
            ->earnings()
            ->with('platform')
            ->whereBetween('earned_at', [$from, $to])
            ->get();
    }

    private function bonuses(
        Shift $shift,
        Carbon $from,
        Carbon $to
    ): Collection {
        if (! $shift->performance) {
            return collect();
        }

        return $shift->performance
            ->bonuses()
            ->with('user')
            ->whereBetween('created_at', [$from, $to])
            ->get();
    }

    private function penalties(
        Shift $shift,
        Carbon $from,
        Carbon $to
    ): Collection {
        if (! $shift->performance) {
            return collect();
        }

        return $shift->performance
            ->penalties()
            ->with('user')
            ->whereBetween('created_at', [$from, $to])
            ->get();
    }

    private function deductions(
        Shift $shift,
        Carbon $from,
        Carbon $to
    ): Collection {
        if (! $shift->performance) {
            return collect();
        }

        return $shift->performance
            ->deductions()
            ->with('user')
            ->whereBetween('created_at', [$from, $to])
            ->get();
    }
}