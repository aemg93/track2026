<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ShiftStatus;
use App\Models\Performance;
use App\Models\Shift;
use App\Models\User;

class ShiftDashboardService
{
    public function __construct(
        private ShiftService $shiftService,
        private ShiftActivityService $shiftActivityService,
    ) {}



    public function data(User $user): array
    {
        $shifts = $user->hasRole('Super Admin')
            ? $this->shiftService->active()
            : $this->shiftService->activeByStudioId(
                (int) $user->studio_id
            );


        return [

            'total_active_shifts' =>
                $shifts->count(),



            'active_models' =>
                $shifts
                    ->filter(
                        fn(Shift $shift) =>
                            $shift->isActive()
                    )
                    ->pluck('performance_id')
                    ->unique()
                    ->count(),



            'paused_models' =>
                $shifts
                    ->filter(
                        fn(Shift $shift) =>
                            $shift->isPaused()
                    )
                    ->pluck('performance_id')
                    ->unique()
                    ->count(),



            'active_shifts' =>
                $shifts
                    ->filter(
                        fn(Shift $shift) =>
                            $shift->performance !== null
                    )
                    ->map(
                        fn(Shift $shift) =>
                            $this->buildShift($shift)
                    )
                    ->values()
                    ->toArray(),

        ];
    }





    private function buildShift(Shift $shift): array
    {
        $workedSeconds =
            $shift->workedSeconds();



        $activity =
            $this->shiftActivityService
                ->activity($shift);



        return [

            'id' =>
                $shift->id,



            'status' =>
                $shift->status?->value,



            'started_at' =>
                $shift->started_at,



            'last_resumed_at' =>
                $shift->last_resumed_at,



            'paused_at' =>
                $shift->paused_at,



            'ended_at' =>
                $shift->ended_at,



            'worked_seconds' =>
                $workedSeconds,



            'total_paused_seconds' =>
                (int) $shift->total_paused_seconds,



            'duration' => [

                'minutes' =>
                    intdiv(
                        $workedSeconds,
                        60
                    ),


                'hours' =>
                    round(
                        $workedSeconds / 3600,
                        2
                    ),


                'formatted' =>
                    gmdate(
                        'H:i:s',
                        $workedSeconds
                    ),

            ],





            'performance' =>
                $shift->performance
                    ? [

                        'id' =>
                            $shift->performance->id,


                        'name' =>
                            trim(
                                $shift->performance->first_name .
                                ' ' .
                                $shift->performance->last_name
                            ),


                        'nickname' =>
                            $shift->performance->nickname,


                        'platforms' =>
                            $this->buildPlatforms(
                                $shift->performance
                            ),

                    ]
                    : null,






            'studio' =>
                $shift->studio
                    ? [

                        'id' =>
                            $shift->studio->id,


                        'name' =>
                            $shift->studio->name,

                    ]
                    : null,






            'actions' => [

                'can_pause' =>
                    $shift->isActive(),


                'can_resume' =>
                    $shift->isPaused(),


                'can_finish' =>
                    ! $shift->isFinished(),

            ],






            'activity' => [

                'timeline' =>
                    $activity['timeline'],


                'metrics' =>
                    $activity['summary'],


                'financial_summary' =>
                    $activity['financial_summary'],

            ],

        ];
    }





    private function buildPlatforms(
        Performance $performance
    ): array {

        return $performance
            ->platforms
            ->map(
                fn($platform) => [

                    'id' =>
                        $platform->id,


                    'name' =>
                        $platform->name,


                    'type' =>
                        $platform->type,

                ]
            )
            ->values()
            ->toArray();
    }






    public function activePerformances(): array
    {
        return Shift::query()

            ->whereHas('performance')


            ->whereIn('status', [

                ShiftStatus::Active->value,

                ShiftStatus::Paused->value,

            ])


            ->with([

                'performance:id,studio_id,first_name,last_name,nickname',

            ])


            ->get()


            ->filter(
                fn(Shift $shift) =>
                    $shift->performance !== null
            )


            ->map(
                fn(Shift $shift) => [

                    'id' =>
                        $shift->performance->id,


                    'name' =>
                        trim(
                            $shift->performance->first_name .
                            ' ' .
                            $shift->performance->last_name
                        ),


                    'nickname' =>
                        $shift->performance->nickname,


                    'status' =>
                        $shift->status?->value,

                ]
            )


            ->values()


            ->toArray();
    }
}