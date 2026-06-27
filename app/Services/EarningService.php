<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EarningService
{
    public function list(User $user): LengthAwarePaginator
    {
        $query = Earning::query()
            ->with([
                'performance:id,studio_id,user_id,first_name,last_name,nickname',
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

    public function syncEarning(
        Earning $earning
    ): Earning {

        $this->calculateBonus($earning);

        $this->calculatePenalty($earning);

        $this->calculateDeduction($earning);

        $this->calculateNet($earning);

        $this->calculateShares($earning);

        $earning->save();

        return $earning;
    }

    private function calculateBonus(
        Earning $earning
    ): void {

        $earning->bonus_usd = $this->sumAmountForPeriod(
            $earning->performance->bonuses(),
            $earning
        );
    }

    private function calculatePenalty(
        Earning $earning
    ): void {

        $earning->penalty_usd = $this->sumAmountForPeriod(
            $earning->performance->penalties(),
            $earning
        );
    }

    private function calculateDeduction(
        Earning $earning
    ): void {

        $earning->deduction_usd = $this->sumAmountForPeriod(
            $earning->performance->deductions(),
            $earning
        );
    }

    private function calculateNet(
        Earning $earning
    ): void {

        $earning->net_usd = round(

            (
                (float) $earning->gross_usd
                + (float) $earning->bonus_usd
                - (float) $earning->penalty_usd
                - (float) $earning->deduction_usd
            ),

            2

        );
    }

    private function calculateShares(
        Earning $earning
    ): void {

        $earning->model_share_usd = round(

            $earning->net_usd *
            (
                (float) $earning->model_percentage / 100
            ),

            2

        );

        $earning->studio_share_usd = round(

            $earning->net_usd *
            (
                (float) $earning->studio_percentage / 100
            ),

            2

        );
    }

    private function sumAmountForPeriod(
        HasMany $relation,
        Earning $earning
    ): float {

        return round(

            (float) $relation
                ->whereBetween(
                    'date',
                    [
                        $earning->period_start,
                        $earning->period_end,
                    ]
                )
                ->sum('amount'),

            2

        );
    }

    private function paginate($query): LengthAwarePaginator
    {
        return $query
            ->orderByDesc('period_end')
            ->paginate(25);
    }
}