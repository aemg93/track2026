<?php

namespace App\Services;

use App\Models\Performance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceService
{
    public function __construct(
        private SplitService $splitService
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

            return $performance
                ->fresh()
                ->load($this->relations());
        });
    }

    public function update(
        int $id,
        array $data
    ): Performance {
        return DB::transaction(function () use ($id, $data) {
            $performance = Performance::findOrFail($id);

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

            return $performance
                ->fresh()
                ->load($this->relations());
        });
    }

    public function delete(int $id): void
    {
        Performance::findOrFail($id)->delete();
    }

    public function leaderboard(int $limit = 20)
    {
        $limit = max(
            1,
            min($limit, 100)
        );

        return Performance::query()
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
            ->where('active', true)
            ->orderByDesc('hours_streamed')
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