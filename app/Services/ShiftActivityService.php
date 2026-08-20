<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ShiftActivityService
{
    public function __construct(
        private ShiftFinancialSummaryService $financialSummary
    ) {
    }

    public function activity(Shift $shift): array
    {
        [$from, $to] = $this->range($shift);

        $data = [
            'earnings' => $this->earnings(
                $shift,
                $from,
                $to
            ),

            'bonuses' => $this->bonuses(
                $shift,
                $from,
                $to
            ),

            'penalties' => $this->penalties(
                $shift,
                $from,
                $to
            ),

            'deductions' => $this->deductions(
                $shift,
                $from,
                $to
            ),
        ];

        $summary = $this->financialSummary->summary($shift);

        return [
            'timeline' => $this->buildTimeline($data),

            'summary' => $summary,

            'financial_summary' => $summary,

            'platforms' => $summary['platforms'] ?? [],
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
        $from = $shift->started_at instanceof Carbon
            ? $shift->started_at
            : Carbon::parse($shift->started_at);

        $to = $shift->ended_at
            ? (
                $shift->ended_at instanceof Carbon
                    ? $shift->ended_at
                    : Carbon::parse($shift->ended_at)
            )
            : now();

        return [$from, $to];
    }

    private function buildTimeline(array $data): array
    {
        return collect()
            ->merge(
                $data['earnings']->map(
                    fn ($earning): array => [
                        'id' => $earning->id,
                        'type' => 'earning',
                        'title' => 'Ganancia',
                        'description' => $earning->platform?->name,
                        'performed_by' => $earning->user?->name,
                        'amount' => (float) (
                            $earning->gross_usd ?? 0
                        ),
                        'tokens' => $earning->real_tokens !== null
                            ? (float) $earning->real_tokens
                            : null,
                        'real_tokens' => $earning->real_tokens !== null
                            ? (float) $earning->real_tokens
                            : null,
                        'currency' => $earning->original_currency,
                        'date' => $earning->earned_at,
                        'created_at' => $earning->created_at,
                        'earned_at' => $earning->earned_at,
                    ]
                )
            )
            ->merge(
                $data['bonuses']->map(
                    fn ($bonus): array => [
                        'id' => $bonus->id,
                        'type' => 'bonus',
                        'title' => 'Bono',
                        'description' => $bonus->reason,
                        'performed_by' => $bonus->user?->name,
                        'amount' => (float) (
                            $bonus->amount ?? 0
                        ),
                        'tokens' => null,
                        'real_tokens' => null,
                        'currency' => 'usd',
                        'date' => $bonus->date,
                        'created_at' => $bonus->created_at,
                    ]
                )
            )
            ->merge(
                $data['penalties']->map(
                    fn ($penalty): array => [
                        'id' => $penalty->id,
                        'type' => 'penalty',
                        'title' => 'Penalización',
                        'description' => $penalty->reason,
                        'performed_by' => $penalty->user?->name,
                        'amount' => (float) (
                            $penalty->amount ?? 0
                        ),
                        'tokens' => null,
                        'real_tokens' => null,
                        'currency' => 'usd',
                        'date' => $penalty->date,
                        'created_at' => $penalty->created_at,
                    ]
                )
            )
            ->merge(
                $data['deductions']->map(
                    fn ($deduction): array => [
                        'id' => $deduction->id,
                        'type' => 'deduction',
                        'title' => 'Descuento',
                        'description' => $deduction->reason,
                        'performed_by' => $deduction->user?->name,
                        'amount' => (float) (
                            $deduction->amount ?? 0
                        ),
                        'tokens' => null,
                        'real_tokens' => null,
                        'currency' => 'usd',
                        'date' => $deduction->date,
                        'created_at' => $deduction->created_at,
                    ]
                )
            )
            ->sortByDesc('date')
            ->values()
            ->toArray();
    }

    private function earnings(
        Shift $shift,
        Carbon $from,
        Carbon $to
    ): Collection {
        if (! $shift->performance) {
            return collect();
        }

        return $shift
            ->performance
            ->earnings()
            ->with([
                'platform:id,name,type',
                'user:id,name',
            ])
            ->whereBetween(
                'earned_at',
                [
                    $from,
                    $to,
                ]
            )
            ->orderBy('earned_at')
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

        return $shift
            ->performance
            ->bonuses()
            ->with([
                'user:id,name',
            ])
            ->whereBetween(
                'date',
                [
                    $from,
                    $to,
                ]
            )
            ->orderBy('date')
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

        return $shift
            ->performance
            ->penalties()
            ->with([
                'user:id,name',
            ])
            ->whereBetween(
                'date',
                [
                    $from,
                    $to,
                ]
            )
            ->orderBy('date')
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

        return $shift
            ->performance
            ->deductions()
            ->with([
                'user:id,name',
            ])
            ->whereBetween(
                'date',
                [
                    $from,
                    $to,
                ]
            )
            ->orderBy('date')
            ->get();
    }
}