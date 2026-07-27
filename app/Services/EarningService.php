<?php

namespace App\Services;

use App\Enums\MonitorShiftStatus;
use App\Models\Earning;
use App\Models\MonitorShift;
use App\Models\Performance;
use App\Models\Platform;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EarningService
{
    public function __construct(
        private RevenueService $revenueService
    ) {
    }

    public function list(User $user): LengthAwarePaginator
    {
        $query = Earning::query()
            ->with([
                'performance:id,studio_id,user_id,first_name,last_name,nickname',
                'platform:id,name,slug',
                'user:id,name,email',
                'monitorShift.monitor:id,name,email',
            ]);


        if ($user->hasRole('Super Admin')) {
            return $this->paginate($query);
        }


        if (
            $user->hasRole('Admin') ||
            $user->hasRole('Monitor')
        ) {

            $query->whereHas(
                'performance',
                function ($q) use ($user) {

                    $q->where(
                        'studio_id',
                        $user->studio_id
                    );

                }
            );
        }


        if ($user->hasRole('Performance')) {

            $query->whereHas(
                'performance',
                function ($q) use ($user) {

                    $q->where(
                        'user_id',
                        $user->id
                    );

                }
            );
        }

        return $this->paginate($query);
    }

    public function create(array $data): Earning
    {
        $performance = Performance::findOrFail(
            $data['performance_id']
        );


        $platform = Platform::findOrFail(
            $data['platform_id']
        );

        $monitorShift = MonitorShift::query()
            ->where(
                'studio_id',
                $performance->studio_id
            )
            ->where(
                'status',
                MonitorShiftStatus::Active
            )
            ->latest('started_at')
            ->first();

        $revenue = $this->revenueService->calculate(
            $performance,
            $platform,
            (float) $data['original_amount']
        );

        $earning = Earning::create([

            'performance_id' =>
                $performance->id,

            'platform_id' =>
                $platform->id,

            'monitor_shift_id' =>
                $monitorShift?->id,

            'user_id' =>
                $data['user_id'] ?? Auth::id(),

            'earned_at' =>
                now(),


            'original_amount' =>
                $revenue['original_amount'],

            'original_currency' =>
                $revenue['original_currency'],

            'real_tokens' =>
                $revenue['real_tokens'] ?? null,

            'conversion_rate' =>
                $revenue['conversion_rate'] ?? null,

            'multiplier' =>
                $revenue['multiplier'] ?? null,

            'gross_usd' =>
                $revenue['gross_usd'],

            'bonus_usd' =>
                0,

            'penalty_usd' =>
                0,

            'deduction_usd' =>
                0,


            'net_usd' =>
                $revenue['net_usd'],

            'model_percentage' =>
                $revenue['model_percentage'],

            'studio_percentage' =>
                $revenue['studio_percentage'],

            'model_share_usd' =>
                $revenue['model_share_usd'],

            'studio_share_usd' =>
                $revenue['studio_share_usd'],

            'status' =>
                $data['status'] ?? 'draft',

            'paid_at' =>
                $data['paid_at'] ?? null,

        ]);

        return $this->syncEarning(
            $earning
        );
    }

    public function syncEarning(
        Earning $earning
    ): Earning {

        $earning->loadMissing([
            'performance',
            'platform',
            'user',
        ]);

        $earning->bonus_usd =
            $this->sumAdjustments(
                $earning->performance->bonuses(),
                $earning
            );

        $earning->penalty_usd =
            $this->sumAdjustments(
                $earning->performance->penalties(),
                $earning
            );

        $earning->deduction_usd =
            $this->sumAdjustments(
                $earning->performance->deductions(),
                $earning
            );

        $earning->net_usd =
            round(
                $earning->gross_usd
                + $earning->bonus_usd
                - $earning->penalty_usd
                - $earning->deduction_usd,
                2
            );

        $earning->model_share_usd =
            round(
                $earning->net_usd *
                ($earning->model_percentage / 100),
                2
            );

        $earning->studio_share_usd =
            round(
                $earning->net_usd *
                ($earning->studio_percentage / 100),
                2
            );

        $earning->save();


        return $earning;
    }

    private function sumAdjustments(
        $relation,
        Earning $earning
    ): float {

        return round(
            (float) $relation
                ->whereDate(
                    'date',
                    $earning->earned_at
                )
                ->sum('amount'),
            2
        );
    }

    private function paginate(
        Builder $query
    ): LengthAwarePaginator {

        return $query
            ->orderByDesc('earned_at')
            ->paginate(25);
    }
}