<?php

namespace App\Services;

use App\Models\Penalty;
use App\Models\Performance;
use App\Services\RankingService;
use App\Services\AuditService;
use Illuminate\Support\Facades\Cache;

class PenaltyService
{
    public function create(array $data): Penalty
    {
        $user = auth()->user();

        $performance = Performance::findOrFail($data['performance_id']);

        if (! $user->can('create', Penalty::class)) {
            abort(403);
        }

        if ($user->hasRole('Performance') || $user->hasRole('Monitor')) {
            abort(403);
        }

        if ($user->hasRole('Admin') && $performance->studio_id !== $user->studio_id) {
            abort(403, 'Outside your studio scope');
        }

        $penalty = Penalty::create([
            'performance_id' => $performance->id,
            'user_id'        => $performance->user_id,
            'reason'         => $data['reason'],
            'amount'         => $data['amount'],
            'date'           => $data['date'],
        ]);

        app(RankingService::class)
            ->recalculate($performance->id);

        app(AuditService::class)
            ->log($penalty, 'created');

        $this->clearStatsCache($performance->id);

        return $penalty;
    }

    private function clearStatsCache(int $performanceId): void
    {
        foreach (['today', 'weekly', 'biweekly', 'monthly'] as $range) {
            Cache::forget("stats:v1:performance:{$performanceId}:{$range}");
        }
    }
}