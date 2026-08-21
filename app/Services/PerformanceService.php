<?php

namespace App\Services;

use App\Models\Performance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PerformanceService
{
    public function __construct(
        private SplitService $splitService,
        private AuditService $auditService
    ) {
    }

    protected function relations(): array
    {
        return [
            'earnings.platform',
            'bonuses',
            'penalties',
            'deductions',
            'split',
            'user',
            'studio',
            'platforms',
        ];
    }

    public function list(Request $request)
    {
        $query = Performance::query();
        $user = $request->user();
        if ($user && ! $user->isSuperAdmin() && $user->isPerformance()) {
            $query->where('user_id', $user->id);
        } elseif ($user && ! $user->isSuperAdmin()) {
            $query->where('studio_id', $user->studio_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($query) use ($search) {
                $query
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('nickname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('active')) {
            $query->where(
                'active',
                filter_var(
                    $request->input('active'),
                    FILTER_VALIDATE_BOOLEAN
                )
            );
        }

        if ($request->filled('work_shift')) {
            $query->where(
                'work_shift',
                $request->input('work_shift')
            );
        }

        $allowedSorts = [
            'created_at',
            'hours_streamed',
        ];

        $sort = $request->input(
            'sortBy',
            'created_at'
        );

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        $order = strtolower(
            $request->input('order', 'desc')
        );

        if (!in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $limit = (int) $request->input(
            'limit',
            10
        );

        $limit = max(
            1,
            min($limit, 100)
        );

        return $query
            ->with($this->relations())
            ->orderBy($sort, $order)
            ->paginate($limit);
    }

    public function find(int $id): Performance
    {
        return Performance::with(
            $this->relations()
        )->findOrFail($id);
    }

    public function create(array $data): Performance
    {
        $user = Auth::user();
        abort_unless($user?->can('create', Performance::class), 403);
        if (! $user->isSuperAdmin() && (int) $data['studio_id'] !== (int) $user->studio_id) {
            abort(403, 'Outside your studio scope');
        }
        if (! $user->isSuperAdmin()) {
            unset($data['split']);
        }
        return DB::transaction(function () use ($data) {
            $data = $this->normalize($data);

            $platforms = $data['platforms'] ?? [];

            $split = $data['split']
                ?? $this->splitService->defaults();

            unset(
                $data['platforms'],
                $data['split']
            );

            $performance = Performance::create($data);

            $this->syncPlatforms(
                $performance,
                $platforms
            );

            $this->syncSplit(
                $performance,
                $split,
                true
            );

            $performance = $performance
                ->fresh()
                ->load($this->relations());
            $this->auditService->log($performance, 'created');
            return $performance;
        });
    }

    public function update(
        int $id,
        array $data
    ): Performance {
        return DB::transaction(function () use ($id, $data) {
            $performance = Performance::findOrFail($id);
            abort_unless(Auth::user()?->can('update', $performance), 403);
            if (! Auth::user()->isSuperAdmin() && array_key_exists('split', $data)) {
                abort(403, 'Only Super Admin may modify split.');
            }

            $data = $this->normalize($data);

            $platforms = $data['platforms'] ?? null;
            $split = $data['split'] ?? null;

            unset(
                $data['platforms'],
                $data['split']
            );

            $performance->update($data);

            $this->syncPlatforms(
                $performance,
                $platforms
            );

            $this->syncSplit(
                $performance,
                $split
            );

            $performance = $performance
                ->fresh()
                ->load($this->relations());
            $this->auditService->log($performance, 'updated');
            return $performance;
        });
    }

    public function delete(int $id): void
    {
        $performance = Performance::findOrFail($id);
        abort_unless(Auth::user()?->can('delete', $performance), 403);
        $performance->delete();
        $this->auditService->log($performance, 'deleted');
    }

    public function leaderboard(int $limit = 20)
    {
        $limit = max(
            1,
            min($limit, 100)
        );

        $query = Performance::query()
            ->select([
                'id',
                'first_name',
                'last_name',
                'nickname',
                'profile_photo',
                'work_shift',
                'hours_streamed',
                'ranking_score',
                'active',
            ])
            ->where('active', true);

        $user = Auth::user();
        if ($user && ! $user->isSuperAdmin()) {
            $column = $user->isPerformance() ? 'user_id' : 'studio_id';
            $value = $user->isPerformance() ? $user->id : $user->studio_id;
            $query->where($column, $value);
        }

        return $query->orderByDesc('hours_streamed')
            ->limit($limit)
            ->get();
    }

    private function normalize(array $data): array
    {
        $data['active'] =
            $data['active'] ?? true;

        $data['hours_streamed'] =
            $data['hours_streamed'] ?? 0;

        $data['ranking_score'] =
            $data['ranking_score'] ?? 0;

        return $data;
    }

    private function syncPlatforms(
        Performance $performance,
        ?array $platforms
    ): void {
        if ($platforms === null) {
            return;
        }

        $performance
            ->platforms()
            ->sync(
                array_unique($platforms)
            );
    }

    private function syncSplit(
        Performance $performance,
        ?array $split,
        bool $creating = false
    ): void {
        if ($split === null) {
            return;
        }

        $this->splitService->validate(
            $split['model_percentage'],
            $split['studio_percentage']
        );

        if ($creating) {
            $performance
                ->split()
                ->create($split);

            return;
        }

        $performance
            ->split()
            ->updateOrCreate(
                [],
                $split
            );
    }
}
