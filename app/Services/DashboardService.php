<?php

namespace App\Services;

use App\Models\User;

class DashboardService
{
    public function __construct(
        private PerformanceDashboardService $performanceDashboard,
        private FinancialDashboardService $financialDashboard,
        private ShiftDashboardService $shiftDashboard,
    ) {}



    public function getData(User $user): array
    {
        return [
            'dashboard' =>
                $this->performanceDashboard
                    ->data($user),


            'finance' =>
                $this->financialDashboard
                    ->data($user),


            'operations' =>
                $this->shiftDashboard
                    ->data($user),
        ];
    }
}