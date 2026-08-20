<?php

namespace App\Services;

use App\Models\Shift;

class ShiftReportService
{
    public function __construct(
        private ShiftActivityService $activityService,
        private ShiftFinancialSummaryService $financialSummaryService,
    ) {}

    public function generate(
        Shift $shift
    ): array {
        $shift->load([
            'performance.platforms',
            'studio',
        ]);

        return [
            'shift' => [
                'id' =>
                    $shift->id,

                'status' =>
                    $shift->status?->value,

                'started_at' =>
                    $shift->started_at,

                'ended_at' =>
                    $shift->ended_at,

                'worked_seconds' =>
                    $shift->workedSeconds(),

                'worked_time' =>
                    $shift->workedTime(),

                'total_paused_seconds' =>
                    (int) $shift->total_paused_seconds,

                'studio_time' =>
                    $shift->studioTime(),
            ],

            'performance' =>
                $this->performance($shift),

            'studio' =>
                $this->studio($shift),

            'financial' =>
                $this->financialSummaryService
                    ->summary($shift),

            'activity' =>
                $this->activityService
                    ->activity($shift),
        ];
    }

    private function performance(
        Shift $shift
    ): ?array {
        $performance = $shift->performance;

        if (! $performance) {
            return null;
        }

        return [
            'id' =>
                $performance->id,

            'name' =>
                trim(
                    "{$performance->first_name} {$performance->last_name}"
                ),

            'nickname' =>
                $performance->nickname,

            'platforms' =>
                $performance->platforms
                    ->map(
                        fn ($platform) => [
                            'id' =>
                                $platform->id,

                            'name' =>
                                $platform->name,

                            'type' =>
                                $platform->type,
                        ]
                    )
                    ->values()
                    ->toArray(),
        ];
    }

    private function studio(
        Shift $shift
    ): ?array {
        if (! $shift->studio) {
            return null;
        }

        return [
            'id' =>
                $shift->studio->id,

            'name' =>
                $shift->studio->name,
        ];
    }
}