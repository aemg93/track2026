<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Earning;
use App\Models\MonitorShift;
use Illuminate\Support\Collection;

class MonitorShiftActivityService
{
    public function activity(
        MonitorShift $monitorShift
    ): array {
        $earnings = Earning::query()
            ->with([
                'performance',
                'platform',
            ])
            ->where(
                'monitor_shift_id',
                $monitorShift->id
            )
            ->whereHas(
                'performance',
                fn ($query) => $query->where(
                    'studio_id',
                    $monitorShift->studio_id
                )
            )
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        return [
            'monitor_shift' => [
                'id' => $monitorShift->id,
                'studio_id' => $monitorShift->studio_id,
                'monitor_id' => $monitorShift->monitor_id,
                'started_at' => $monitorShift->started_at,
                'ended_at' => $monitorShift->ended_at,
                'status' => $monitorShift->status?->value
                    ?? $monitorShift->status,
            ],
            'summary' => $this->buildTotals($earnings),
            'models' => $this->groupModels($earnings),
            'platforms' => $this->groupByPlatforms($earnings),
            'timeline' => $this->buildTimeline($earnings),
        ];
    }

    public function summary(int $studioId): array
    {
        $earnings = Earning::query()
            ->with([
                'performance',
                'platform',
            ])
            ->whereHas(
                'performance',
                fn ($query) => $query->where(
                    'studio_id',
                    $studioId
                )
            )
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        return [
            'summary' => $this->buildTotals($earnings),
            'models' => $this->groupModels($earnings),
            'platforms' => $this->groupByPlatforms($earnings),
            'timeline' => $this->buildTimeline($earnings),
        ];
    }

    private function buildTotals(
        Collection $earnings
    ): array {
        return [
            'total_usd' => round(
                (float) $earnings->sum('gross_usd'),
                2
            ),
            'total_tokens' => $this->totalTokens($earnings),
        ];
    }

    private function groupModels(
        Collection $earnings
    ): array {
        return $earnings
            ->groupBy('performance_id')
            ->map(
                function (
                    Collection $items
                ): ?array {
                    $performance =
                        $items
                            ->first()
                            ?->performance;

                    if ($performance === null) {
                        return null;
                    }

                    return [
                        'performance_id' =>
                            $performance->id,

                        'model' =>
                            $performance->name,

                        'nickname' =>
                            $performance->nickname,

                        'work_shift' =>
                            $performance
                                ->work_shift
                                ?->value,

                        'platforms' =>
                            $this->groupModelPlatforms(
                                $items
                            ),

                        'tokens' =>
                            $this->totalTokens($items),

                        'total_tokens' =>
                            $this->totalTokens($items),

                        'usd' =>
                            round(
                                (float) $items->sum(
                                    'gross_usd'
                                ),
                                2
                            ),

                        'total_usd' =>
                            round(
                                (float) $items->sum(
                                    'gross_usd'
                                ),
                                2
                            ),
                    ];
                }
            )
            ->filter()
            ->sortByDesc('tokens')
            ->values()
            ->toArray();
    }

    private function groupModelPlatforms(
        Collection $earnings
    ): array {
        return $earnings
            ->groupBy('platform_id')
            ->map(
                function (
                    Collection $items
                ): array {
                    $platform =
                        $items
                            ->first()
                            ?->platform;

                    return [
                        'platform_id' =>
                            $platform?->id,

                        'platform' =>
                            $platform?->name,

                        'slug' =>
                            $platform?->slug,

                        'tokens' =>
                            $this->totalTokens($items),

                        'usd' =>
                            round(
                                (float) $items->sum(
                                    'gross_usd'
                                ),
                                2
                            ),
                    ];
                }
            )
            ->sortByDesc('tokens')
            ->values()
            ->toArray();
    }

    private function groupByPlatforms(
        Collection $earnings
    ): array {
        return $earnings
            ->groupBy('platform_id')
            ->map(
                function (
                    Collection $items
                ): array {
                    $platform =
                        $items
                            ->first()
                            ?->platform;

                    return [
                        'platform_id' =>
                            $platform?->id,

                        'platform' =>
                            $platform?->name,

                        'slug' =>
                            $platform?->slug,

                        'tokens' =>
                            $this->totalTokens($items),

                        'usd' =>
                            round(
                                (float) $items->sum(
                                    'gross_usd'
                                ),
                                2
                            ),
                    ];
                }
            )
            ->sortByDesc('tokens')
            ->values()
            ->toArray();
    }

    private function buildTimeline(
        Collection $earnings
    ): array {
        return $earnings
            ->map(
                fn (Earning $earning): array => [
                    'id' =>
                        $earning->id,

                    'type' =>
                        'earning',

                    'performance_id' =>
                        $earning->performance_id,

                    'model' =>
                        $earning
                            ->performance
                            ?->name,

                    'nickname' =>
                        $earning
                            ->performance
                            ?->nickname,

                    'platform_id' =>
                        $earning->platform_id,

                    'platform' =>
                        $earning
                            ->platform
                            ?->name,

                    'amount' =>
                        round(
                            (float) (
                                $earning->gross_usd ?? 0
                            ),
                            2
                        ),

                    'tokens' =>
                        round(
                            (float) (
                                $earning->real_tokens ?? 0
                            ),
                            0
                        ),

                    'created_at' =>
                        $earning->created_at,

                    'earned_at' =>
                        $earning->earned_at,

                    'work_shift' =>
                        $earning
                            ->performance
                            ?->work_shift
                            ?->value,

                    'monitor_shift_id' =>
                        $earning->monitor_shift_id,
                ]
            )
            ->sortByDesc(
                fn (array $item) =>
                    $item['created_at']
            )
            ->values()
            ->toArray();
    }

    private function totalTokens(
        Collection $earnings
    ): float {
        return round(
            (float) $earnings->sum(
                fn (Earning $earning): float =>
                    (float) (
                        $earning->real_tokens ?? 0
                    )
            ),
            0
        );
    }
}
