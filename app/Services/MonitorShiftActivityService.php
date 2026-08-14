<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\MonitorShift;
use Illuminate\Support\Collection;

class MonitorShiftActivityService
{
    public function activity(
        MonitorShift $monitorShift
    ): array {
        $from = $monitorShift->started_at;
        $to = $monitorShift->ended_at ?? now();

        $earnings = Earning::query()
            ->with([
                'performance',
                'platform',
            ])
            ->where(
                'monitor_shift_id',
                $monitorShift->id
            )
            ->whereBetween(
                'earned_at',
                [$from, $to]
            )
            ->whereHas(
                'performance',
                fn ($query) =>
                    $query->where(
                        'studio_id',
                        $monitorShift->studio_id
                    )
            )
            ->get();

        return [
            'summary' => [
                'total_usd' =>
                    (float) $earnings->sum('gross_usd'),

                'total_tokens' =>
                    (float) $earnings
                        ->where(
                            'original_currency',
                            'tokens'
                        )
                        ->sum('original_amount'),
            ],

            'models' =>
                $this->groupByWorkShift(
                    $earnings
                ),

            'platforms' =>
                $this->groupByPlatforms(
                    $earnings
                ),

            'timeline' =>
                $this->buildTimeline(
                    $earnings
                ),
        ];
    }

    /**
     * Agrupa las ganancias por el turno asignado
     * a cada modelo y genera una fila financiera
     * por modelo.
     *
     * Performance.work_shift determina el bloque
     * donde aparece la modelo.
     *
     * earned_at NO determina el turno de la modelo.
     */
    private function groupByWorkShift(
        Collection $earnings
    ): array {
        $groups = [
            'morning' => [],
            'afternoon' => [],
            'night' => [],
        ];

        $earnings
            ->groupBy(
                fn ($earning) =>
                    $earning
                        ->performance
                        ?->work_shift
                        ?->value
            )
            ->each(
                function (
                    Collection $items,
                    ?string $workShift
                ) use (&$groups) {

                    if (
                        ! $workShift ||
                        ! array_key_exists(
                            $workShift,
                            $groups
                        )
                    ) {
                        return;
                    }

                    $items
                        ->groupBy('performance_id')
                        ->each(
                            function (
                                Collection $modelEarnings
                            ) use (
                                &$groups,
                                $workShift
                            ) {

                                $performance =
                                    $modelEarnings
                                        ->first()
                                        ->performance;

                                if (! $performance) {
                                    return;
                                }

                                $platforms =
                                    $this->groupModelPlatforms(
                                        $modelEarnings
                                    );

                                $groups[$workShift][] = [
                                    'performance_id' =>
                                        $performance->id,

                                    'model' =>
                                        $performance->name,

                                    'nickname' =>
                                        $performance->nickname,

                                    'work_shift' =>
                                        $workShift,

                                    'platforms' =>
                                        $platforms,

                                    'total_tokens' =>
                                        (float) $modelEarnings
                                            ->where(
                                                'original_currency',
                                                'tokens'
                                            )
                                            ->sum(
                                                'original_amount'
                                            ),

                                    'total_usd' =>
                                        (float) $modelEarnings
                                            ->sum(
                                                'gross_usd'
                                            ),
                                ];
                            }
                        );
                }
            );

        return $groups;
    }

    /**
     * Agrupa la producción de una modelo
     * por plataforma.
     */
    private function groupModelPlatforms(
        Collection $earnings
    ): array {
        return $earnings
            ->groupBy('platform_id')
            ->map(
                function (
                    Collection $items
                ) {
                    $platform =
                        $items
                            ->first()
                            ->platform;

                    return [
                        'platform_id' =>
                            $platform?->id,

                        'platform' =>
                            $platform?->name,

                        'slug' =>
                            $platform?->slug,

                        'tokens' =>
                            (float) $items
                                ->where(
                                    'original_currency',
                                    'tokens'
                                )
                                ->sum(
                                    'original_amount'
                                ),

                        'usd' =>
                            (float) $items
                                ->sum(
                                    'gross_usd'
                                ),
                    ];
                }
            )
            ->values()
            ->toArray();
    }

    /**
     * Agrupa las ganancias totales por plataforma.
     */
    private function groupByPlatforms(
        Collection $earnings
    ): array {
        return $earnings
            ->groupBy('platform_id')
            ->map(
                function (
                    Collection $items
                ) {
                    $platform =
                        $items
                            ->first()
                            ->platform;

                    return [
                        'platform_id' =>
                            $platform?->id,

                        'platform' =>
                            $platform?->name,

                        'slug' =>
                            $platform?->slug,

                        'usd' =>
                            (float) $items
                                ->sum('gross_usd'),

                        'tokens' =>
                            (float) $items
                                ->where(
                                    'original_currency',
                                    'tokens'
                                )
                                ->sum(
                                    'original_amount'
                                ),
                    ];
                }
            )
            ->sortBy('platform_id')
            ->values()
            ->toArray();
    }

    /**
     * Genera la línea de tiempo de las ganancias.
     */
    private function buildTimeline(
        Collection $earnings
    ): array {
        return $earnings
            ->map(
                fn ($earning) => [
                    'id' =>
                        $earning->id,

                    'type' =>
                        'earning',

                    'model' =>
                        $earning
                            ->performance
                            ?->name,

                    'platform' =>
                        $earning
                            ->platform
                            ?->name,

                    'amount' =>
                        (float) $earning->gross_usd,

                    'tokens' =>
                        $earning->original_currency === 'tokens'
                            ? (float) $earning->original_amount
                            : 0,

                    'date' =>
                        $earning->earned_at,

                    'work_shift' =>
                        $earning
                            ->performance
                            ?->work_shift
                            ?->value,
                ]
            )
            ->values()
            ->toArray();
    }
}