<?php

namespace App\Services;

use App\Models\Penalty;
use App\Models\Performance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PenaltyService
{
    public function __construct(
        private RankingService $rankingService,
        private AuditService $auditService,
        private FinancialSynchronizationService $financialSynchronizationService
    ) {
    }

    public function create(array $data): Penalty
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            abort(401);
        }

        $performance = Performance::findOrFail(
            $data['performance_id']
        );

        if (! $user->can('create', Penalty::class)) {
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
            ! $user->canAccessStudio($performance->studio_id)
        ) {
            abort(
                403,
                'Outside your studio scope'
            );
        }

        $penalty = Penalty::create([

            'performance_id' => $performance->id,

            'user_id' => $performance->user_id,

            'reason' => $data['reason'],

            'amount' => $data['amount'],

            'date' => $data['date'],

        ]);


        $this->rankingService
            ->recalculate($performance->id);

        $this->financialSynchronizationService
            ->synchronizePerformance(
                $performance
            );

        $this->auditService
            ->log(
                $penalty,
                'created'
            );

        return $penalty;
    }
}