<?php

namespace App\Services;

use App\Models\Bonus;
use App\Models\Performance;
use App\Services\RankingService;
use App\Services\AuditService;
use Illuminate\Support\Facades\Cache;

class BonusService
{
    public function create(array $data): Bonus
    {
        $user = auth()->user();

        $performance = Performance::findOrFail($data['performance_id']);

        if (! $user->can('create', Bonus::class)) {
            abort(403);
        }

        if ($user->hasRole('Performance') || $user->hasRole('Monitor')) {
            abort(403);
        }

        if ($user->hasRole('Admin') && $performance->studio_id !== $user->studio_id) {
            abort(403, 'Outside your studio scope');
        }

        $bonus = Bonus::create([
            'performance_id' => $performance->id,
            'user_id'        => $performance->user_id,
            'reason'         => $data['reason'],
            'amount'         => $data['amount'],
            'date'           => $data['date'],
        ]);

        app(RankingService::class)
            ->recalculate($performance->id);

        app(AuditService::class)
            ->log($bonus, 'created');

        $this->clearStatsCache($performance->id);

        return $bonus;
    }

    private function clearStatsCache(int $performanceId): void
    {
        foreach (['today', 'weekly', 'biweekly', 'monthly'] as $range) {
            Cache::forget("stats:v1:performance:{$performanceId}:{$range}");
        }
    }
}