<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Earning;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ShiftFinancialSummaryService
{
    private const UNKNOWN_PLATFORM = 'Sin plataforma';

    public function __construct(
        private FinancialSummaryService $financialSummary
    ) {
    }

    /**
     * Obtiene el resumen financiero exacto del turno.
     *
     * El período utilizado es:
     *
     * started_at -> ended_at
     *
     * Si el turno continúa activo:
     *
     * started_at -> now()
     */
    public function summary(Shift $shift): array
    {
        $performance = $shift->performance;

        if (! $performance) {
            return $this->emptySummary();
        }

        $start = $shift->started_at;
        $end = $shift->ended_at ?? now();

        if (! $start instanceof Carbon) {
            $start = Carbon::parse($start);
        }

        if (! $end instanceof Carbon) {
            $end = Carbon::parse($end);
        }

        if ($end->lt($start)) {
            throw new InvalidArgumentException(
                'El ended_at del Shift no puede ser anterior a started_at.'
            );
        }

        $financial = $this->financialSummary->summaryBetween(
            $performance,
            $start,
            $end,
            true
        );

        $earnings = $this->earnings(
            $shift,
            $start,
            $end
        );

        return [
            'gross_usd' => round(
                (float) ($financial['gross_usd'] ?? 0),
                2
            ),

            'bonus_usd' => round(
                (float) ($financial['bonus_usd'] ?? 0),
                2
            ),

            'penalty_usd' => round(
                (float) ($financial['penalty_usd'] ?? 0),
                2
            ),

            'deduction_usd' => round(
                (float) ($financial['deduction_usd'] ?? 0),
                2
            ),

            'net_usd' => round(
                (float) ($financial['net_usd'] ?? 0),
                2
            ),

            'model_percentage' => (float) (
                $financial['model_percentage'] ?? 0
            ),

            'studio_percentage' => (float) (
                $financial['studio_percentage'] ?? 0
            ),

            'model_share_usd' => round(
                (float) ($financial['model_share_usd'] ?? 0),
                2
            ),

            'studio_share_usd' => round(
                (float) ($financial['studio_share_usd'] ?? 0),
                2
            ),

            'total_tokens' => $this->totalTokens($earnings),

            'platforms' => $this->groupByPlatform($earnings),
        ];
    }

    /**
     * Obtiene los earnings pertenecientes al intervalo del turno.
     */
    protected function earnings(
        Shift $shift,
        Carbon $start,
        Carbon $end
    ): Collection {
        $performance = $shift->performance;

        if (! $performance) {
            return collect();
        }

        return $performance
            ->earnings()
            ->with([
                'platform:id,name,type',
            ])
            ->whereBetween(
                'earned_at',
                [
                    $start,
                    $end,
                ]
            )
            ->orderBy('earned_at')
            ->get();
    }

    /**
     * Total de tokens reales generados durante el turno.
     */
    protected function totalTokens(
        Collection $earnings
    ): float {
        return round(
            (float) $earnings->sum(
                static fn (Earning $earning): float =>
                    (float) ($earning->real_tokens ?? 0)
            ),
            0
        );
    }

    /**
     * Agrupa los earnings por plataforma.
     */
    protected function groupByPlatform(
        Collection $earnings
    ): array {
        return $earnings
            ->groupBy(
                static fn (Earning $earning) =>
                    $earning->platform?->id
                    ?? self::UNKNOWN_PLATFORM
            )
            ->map(
                static function (Collection $items): array {
                    $first = $items->first();
                    $platform = $first?->platform;

                    return [
                        'platform_id' => $platform?->id,

                        'platform_name' =>
                            $platform?->name
                            ?? self::UNKNOWN_PLATFORM,

                        'type' => $platform?->type,

                        'currencies' => $items
                            ->pluck('original_currency')
                            ->filter()
                            ->unique()
                            ->map(
                                static fn ($currency): string =>
                                    strtoupper(
                                        (string) $currency
                                    )
                            )
                            ->values()
                            ->toArray(),

                        'original_amount' => round(
                            (float) $items->sum(
                                static fn (Earning $earning): float =>
                                    (float) (
                                        $earning->original_amount ?? 0
                                    )
                            ),
                            2
                        ),

                        'real_tokens' => round(
                            (float) $items->sum(
                                static fn (Earning $earning): float =>
                                    (float) (
                                        $earning->real_tokens ?? 0
                                    )
                            ),
                            0
                        ),

                        'gross_usd' => round(
                            (float) $items->sum(
                                static fn (Earning $earning): float =>
                                    (float) (
                                        $earning->gross_usd ?? 0
                                    )
                            ),
                            2
                        ),
                    ];
                }
            )
            ->sortByDesc(
                static fn (array $platform): float =>
                    (float) $platform['real_tokens']
            )
            ->values()
            ->toArray();
    }

    /**
     * Resumen vacío cuando el turno no tiene Performance.
     */
    protected function emptySummary(): array
    {
        return [
            'gross_usd' => 0.0,
            'bonus_usd' => 0.0,
            'penalty_usd' => 0.0,
            'deduction_usd' => 0.0,
            'net_usd' => 0.0,

            'model_percentage' => 0.0,
            'studio_percentage' => 0.0,

            'model_share_usd' => 0.0,
            'studio_share_usd' => 0.0,

            'total_tokens' => 0.0,

            'platforms' => [],
        ];
    }
}
