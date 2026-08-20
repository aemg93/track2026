<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bonus;
use App\Models\Performance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BonusService
{
    public function __construct(
        private RankingService $rankingService,
        private AuditService $auditService,
        private FinancialSyncDispatcher $financialSyncDispatcher
    ) {
    }

    public function create(array $data): Bonus
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            abort(401);
        }

        $performance = Performance::findOrFail(
            $data['performance_id']
        );

        if (! $user->can('create', Bonus::class)) {
            abort(403);
        }

        if (
            $user->isPerformance() ||
            $user->isMonitor()
        ) {
            abort(403);
        }

        if (
            $user->isAdmin() &&
            ! $user->canAccessStudio(
                $performance->studio_id
            )
        ) {
            abort(
                403,
                'Outside your studio scope'
            );
        }

        $bonus = Bonus::create([
            'performance_id' => $performance->id,
            'user_id' => $user->id,
            'reason' => $data['reason'],
            'amount' => $data['amount'],

            // Hora real del servidor.
            'date' => now(),
        ]);

        $this->financialSyncDispatcher
            ->dispatchPerformance($performance);

        $this->rankingService
            ->recalculate($performance->id);

        $this->auditService
            ->log($bonus, 'created');

        return $bonus;
    }
}