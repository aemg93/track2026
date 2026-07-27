<?php

namespace App\Services;

use App\Models\MonitorShift;
use App\Models\Earning;
use Carbon\Carbon;
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
                    $earnings->sum('gross_usd'),

                'total_tokens' =>
                    $earnings
                        ->where(
                            'original_currency',
                            'tokens'
                        )
                        ->sum('original_amount'),

            ],


            'models' =>
                $this->groupByModels($earnings),


            'platforms' =>
                $this->groupByPlatforms($earnings),


            'timeline' =>
                $earnings->map(fn ($earning) => [

                    'id' => $earning->id,

                    'type' => 'earning',

                    'model' =>
                        $earning->performance?->name,

                    'platform' =>
                        $earning->platform?->name,

                    'amount' =>
                        (float) $earning->gross_usd,

                    'date' =>
                        $earning->earned_at,

                ])->values()->toArray(),

        ];
    }



    private function groupByModels(
        Collection $earnings
    ): array {

        return $earnings
            ->groupBy(
                'performance_id'
            )
            ->map(fn ($items) => [

                'model' =>
                    $items->first()
                        ->performance?->name,

                'usd' =>
                    $items->sum('gross_usd'),

            ])
            ->values()
            ->toArray();
    }



    private function groupByPlatforms(
        Collection $earnings
    ): array {

        return $earnings
            ->groupBy(
                'platform_id'
            )
            ->map(fn ($items) => [

                'platform' =>
                    $items->first()
                        ->platform?->name,

                'usd' =>
                    $items->sum('gross_usd'),

            ])
            ->values()
            ->toArray();
    }
}