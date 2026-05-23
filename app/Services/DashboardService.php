<?php

namespace App\Services;

use App\Models\User;
use App\Models\Performance;

class DashboardService
{
    public function getData(User $user): array
    {
        if ($user->hasRole('Super Admin')) {
            return $this->superAdminData();
        }

        if ($user->hasRole('Admin')) {
            return $this->adminData($user);
        }

        if ($user->hasRole('Monitor')) {
            return $this->monitorData($user);
        }

        if ($user->hasRole('Performance')) {
            return $this->performanceData($user);
        }

        return [];
    }

    /**
     * 🔴 SUPER ADMIN (GLOBAL VIEW)
     */
    private function superAdminData(): array
    {
        return [
            'view' => 'super_admin',

            'total_models' => Performance::count(),

            'ranking' => Performance::orderByDesc('ranking_score')
                ->take(10)
                ->get(),

            'models' => Performance::with('user')
                ->get()
                ->map(function ($performance) {

                    return [
                        'name' => $performance->name,

                        'statistics' => app(StatisticsService::class)
                            ->performanceStats($performance),
                    ];
                }),
        ];
    }

    /**
     * 🟠 ADMIN (STUDIO ONLY)
     */
    private function adminData(User $user): array
    {
        $performances = Performance::where('studio_id', $user->studio_id)->get();

        return [
            'view' => 'admin',

            'total_models' => $performances->count(),

            'ranking' => $performances
                ->sortByDesc('ranking_score')
                ->values(),

            'models' => $performances->map(function ($performance) {

                return [
                    'name' => $performance->name,

                    'statistics' => app(StatisticsService::class)
                        ->performanceStats($performance),
                ];
            }),
        ];
    }

    /**
     * 🟡 MONITOR (STUDIO LIMITED VIEW)
     */
    private function monitorData(User $user): array
    {
        $performances = Performance::where('studio_id', $user->studio_id)->get();

        return [
            'view' => 'monitor',

            'ranking' => $performances
                ->sortByDesc('ranking_score')
                ->values(),

            'models' => $performances->map(function ($performance) {

                $stats = app(StatisticsService::class)
                    ->performanceStats($performance);

                return [
                    'name' => $performance->name,

                    'today' => [
                        'gross_usd' => $stats['today']['gross_usd'],
                        'model_usd' => $stats['today']['model_usd'],
                    ],
                ];
            }),
        ];
    }

    /**
     * 🔵 PERFORMANCE (OWN DATA ONLY)
     */
    private function performanceData(User $user): array
    {
        $performance = $user->performance;

        return [
            'view' => 'performance',

            'name' => $performance->name,

            'ranking' => $performance->ranking_score,

            'hours' => $performance->hours_streamed,

            'statistics' => app(StatisticsService::class)
                ->performanceStats($performance),
        ];
    }
}