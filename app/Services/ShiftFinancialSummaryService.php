<?php

namespace App\Services;

use App\Models\Shift;
use Illuminate\Support\Collection;

class ShiftFinancialSummaryService
{
    private const UNKNOWN_PLATFORM = 'Sin plataforma';

    public function summary(Shift $shift): array
    {
        $earnings = $this->earnings($shift);

        return [
            'total_usd' => round(
                $earnings->sum('gross_usd'),
                2
            ),

            'total_tokens' => round(
                $earnings->sum(
                    fn ($earning) => (float) ($earning->real_tokens ?? 0)
                ),
                0
            ),

            'platforms' => $this->groupByPlatform($earnings),
        ];
    }

    /**
     * @return Collection<int,\App\Models\Earning>
     */
    protected function earnings(Shift $shift): Collection
    {
        if (! $shift->performance) {
            return collect();
        }

        return $shift
            ->performance
            ->earnings()
            ->with([
                'platform:id,name,type',
            ])
            ->whereBetween(
                'earned_at',
                [
                    $shift->started_at,
                    $shift->ended_at ?? now(),
                ]
            )
            ->get();
    }

    /**
     * @param Collection<int,\App\Models\Earning> $earnings
     */
    protected function groupByPlatform(
        Collection $earnings
    ): array {
        return $earnings
            ->groupBy(
                fn ($earning) =>
                    $earning->platform?->name
                    ?? self::UNKNOWN_PLATFORM
            )
            ->map(
                function (
                    Collection $items,
                    string $platformName
                ) {
                    $first = $items->first();

                    return [
                        'platform_id' =>
                            $first?->platform?->id,

                        'platform_name' =>
                            $platformName,

                        'type' =>
                            $first?->platform?->type,

                        'currencies' =>
                            $items
                                ->pluck('original_currency')
                                ->filter()
                                ->unique()
                                ->map(
                                    fn ($currency) =>
                                        strtoupper($currency)
                                )
                                ->values()
                                ->toArray(),

                        'original_amount' =>
                            round(
                                $items->sum('original_amount'),
                                2
                            ),

                        'real_tokens' =>
                            round(
                                $items->sum(
                                    fn ($earning) =>
                                        (float) (
                                            $earning->real_tokens ?? 0
                                        )
                                ),
                                0
                            ),

                        'usd' =>
                            round(
                                $items->sum('gross_usd'),
                                2
                            ),
                    ];
                }
            )
            ->sortByDesc(
                fn ($platform) =>
                    $platform['real_tokens']
            )
            ->values()
            ->toArray();
    }
}