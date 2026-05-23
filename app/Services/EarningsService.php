<?php

namespace App\Services;

use App\Models\Earning;
use App\Models\Platform;
use App\Models\Performance;
use App\Services\RankingService;
use App\Services\AuditService;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\Cache;

class EarningsService
{
    public function create(array $data): Earning
    {
        $user = auth()->user();

        $platform = Platform::findOrFail($data['platform_id']);
        $performance = Performance::findOrFail($data['performance_id']);

        if (! $user->can('create', Earning::class)) {
            abort(403);
        }

        if ($user->hasRole('Performance') && $performance->user_id !== $user->id) {
            abort(403);
        }

        if ($user->hasRole('Monitor') && $performance->studio_id !== $user->studio_id) {
            abort(403);
        }

        $amountUsd = $this->convertToUsd($data['amount'], $platform);

        $earning = Earning::create([
            'performance_id' => $performance->id,
            'platform_id'    => $platform->id,
            'user_id'        => $performance->user_id,
            'amount'         => $data['amount'],
            'amount_usd'     => $amountUsd,
            'date'           => $data['date'],
        ]);

        app(RankingService::class)
            ->recalculate($performance->id);

        app(AuditService::class)
            ->log($earning, 'created');

        $this->clearStatsCache($performance->id);

        return $earning;
    }

    private function convertToUsd(
        float $amount,
        Platform $platform
    ): float {
        if ($platform->type === 'usd') {
            return $amount;
        }

        $realTokens = $amount * $platform->multiplier;

        return $realTokens * $platform->conversion_rate;
    }

    private function clearStatsCache(int $performanceId): void
    {
        foreach (['today', 'weekly', 'biweekly', 'monthly'] as $range) {
            Cache::forget("stats:v1:performance:{$performanceId}:{$range}");
        }
    }
}