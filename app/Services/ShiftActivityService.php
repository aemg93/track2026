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
            'summary'  => $this->buildSummary($data),
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
        return collect()

            ->merge(
                $data['earnings']->map(fn ($earning) => [

                    'id' => $earning->id,

                    'type' => 'earning',

                    'title' => 'Ganancia',

                    'description' => $earning->platform?->name,

                    'performed_by' => $earning->user?->name,

                    'amount' => (float) $earning->gross_usd,

                    'tokens' => $earning->real_tokens !== null
                        ? (float) $earning->real_tokens
                        : null,

                    'currency' => $earning->original_currency,

                    'date' => $earning->earned_at,

                ])
            )

            ->merge(
                $data['bonuses']->map(fn ($bonus) => [

                    'id' => $bonus->id,

                    'type' => 'bonus',

                    'title' => 'Bono',

                    'description' => $bonus->reason,

                    'performed_by' => $bonus->user?->name,

                    'amount' => (float) $bonus->amount,

                    'tokens' => null,

                    'currency' => 'usd',

                    'date' => $bonus->created_at,

                ])
            )

            ->merge(
                $data['penalties']->map(fn ($penalty) => [

                    'id' => $penalty->id,

                    'type' => 'penalty',

                    'title' => 'Penalización',

                    'description' => $penalty->reason,

                    'performed_by' => $penalty->user?->name,

                    'amount' => (float) $penalty->amount,

                    'tokens' => null,

                    'currency' => 'usd',

                    'date' => $penalty->created_at,

                ])
            )

            ->merge(
                $data['deductions']->map(fn ($deduction) => [

                    'id' => $deduction->id,

                    'type' => 'deduction',

                    'title' => 'Descuento',

                    'description' => $deduction->reason,

                    'performed_by' => $deduction->user?->name,

                    'amount' => (float) $deduction->amount,

                    'tokens' => null,

                    'currency' => 'usd',

                    'date' => $deduction->created_at,

                ])
            )

            ->sortByDesc('date')
            ->values()
            ->toArray();
    }

    private function buildSummary(array $data): array
    {
        $earnings = $data['earnings']->sum('gross_usd');

        $tokens = $data['earnings']->sum(
            fn ($earning) => (float) ($earning->real_tokens ?? 0)
        );

        $bonuses = $data['bonuses']->sum('amount');

        $penalties = $data['penalties']->sum('amount');

        $deductions = $data['deductions']->sum('amount');

        return [

            'earnings' => round($earnings, 2),

            'tokens' => round($tokens, 0),

            'bonuses' => round($bonuses, 2),

            'penalties' => round($penalties, 2),

            'deductions' => round($deductions, 2),

            'net' => round(
                $earnings + $bonuses - $penalties - $deductions,
                2
            ),

        ];
    }

    private function earnings(
        Shift $shift,
        Carbon $from,
        Carbon $to
    ): Collection {

        return $shift->performance
            ? $shift->performance
                ->earnings()
                ->with([
                    'platform',
                    'user:id,name',
                ])
                ->whereBetween('earned_at', [$from, $to])
                ->get()
            : collect();
    }

    private function bonuses(
        Shift $shift,
        Carbon $from,
        Carbon $to
    ): Collection {

        return $shift->performance
            ? $shift->performance
                ->bonuses()
                ->with('user:id,name')
                ->whereBetween('created_at', [$from, $to])
                ->get()
            : collect();
    }

    private function penalties(
        Shift $shift,
        Carbon $from,
        Carbon $to
    ): Collection {

        return $shift->performance
            ? $shift->performance
                ->penalties()
                ->with('user:id,name')
                ->whereBetween('created_at', [$from, $to])
                ->get()
            : collect();
    }

    private function deductions(
        Shift $shift,
        Carbon $from,
        Carbon $to
    ): Collection {

        return $shift->performance
            ? $shift->performance
                ->deductions()
                ->with('user:id,name')
                ->whereBetween('created_at', [$from, $to])
                ->get()
            : collect();
    }
}