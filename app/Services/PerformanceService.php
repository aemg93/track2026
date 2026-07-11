<?php

namespace App\Services;

use App\Models\Performance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PerformanceService
{
    public function __construct(
        private SplitService $splitService
    ) {
    }

    protected function relations(): array
    {
        return [
            'earnings',
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
            $search = trim($request->search);

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
                    $request->active,
                    FILTER_VALIDATE_BOOLEAN
                )
            );
        }

        $allowedSorts = [
            'created_at',
            'hours_streamed',
        ];

        $sort = $request->get(
            'sortBy',
            'created_at'
        );

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        return $query
            ->with($this->relations())
            ->orderBy(
                $sort,
                $request->get('order', 'desc')
            )
            ->paginate(
                (int) $request->get('limit', 10)
            );
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

            $platforms = $data['platforms']
                ?? null;

            $split = $data['split']
                ?? null;

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
        Performance::findOrFail($id)
            ->delete();
    }

    public function leaderboard(
        int $limit = 20
    ) {

        return Performance::query()
            ->select([
                'id',
                'first_name',
                'last_name',
                'nickname',
                'profile_photo',
                'hours_streamed',
                'ranking_score',
                'active',
            ])
            ->where(
                'active',
                true
            )
            ->orderByDesc(
                'hours_streamed'
            )
            ->limit($limit)
            ->get();
    }

    public function storeRules(): array
    {
        return [

            'studio_id' =>
                'required|exists:studios,id',

            'user_id' =>
                'nullable|exists:users,id',

            'first_name' =>
                'required|string|max:255',

            'last_name' =>
                'required|string|max:255',

            'nickname' =>
                'nullable|string|max:255',

            'email' =>
                'required|email|unique:performances,email',

            'phone' =>
                'nullable|string|max:255',

            'country' =>
                'nullable|string|max:255',

            'city' =>
                'nullable|string|max:255',

            'address' =>
                'nullable|string',

            'birth_date' => [
                'required',
                'date',
                'before_or_equal:' .
                now()->subYears(18)->toDateString(),
            ],

            'profile_photo' =>
                'nullable|string',

            'active' =>
                'nullable|boolean',

            'hours_streamed' =>
                'nullable|numeric|min:0',

            'ranking_score' =>
                'nullable|numeric|min:0',

            'platforms' =>
                'required|array|min:1',

            'platforms.*' =>
                'exists:platforms,id',

            'split' =>
                'required|array',

            'split.model_percentage' =>
                'required|numeric|min:0|max:100',

            'split.studio_percentage' =>
                'required|numeric|min:0|max:100',
        ];
    }

    public function updateRules(int $id): array
    {
        return [

            'first_name' =>
                'nullable|string|max:255',

            'last_name' =>
                'nullable|string|max:255',

            'nickname' =>
                'nullable|string|max:255',

            'email' => [
                'nullable',
                'email',
                Rule::unique('performances')
                    ->ignore($id),
            ],

            'birth_date' => [
                'nullable',
                'date',
                'before_or_equal:' .
                now()->subYears(18)->toDateString(),
            ],

            'platforms' =>
                'nullable|array',

            'platforms.*' =>
                'exists:platforms,id',

            'split' =>
                'nullable|array',

            'split.model_percentage' =>
                'required_with:split|numeric|min:0|max:100',

            'split.studio_percentage' =>
                'required_with:split|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [

            'birth_date.before_or_equal' =>
                'Debes ser mayor de edad para registrarte.',

            'studio_id.required' =>
                'Debes seleccionar un estudio.',

            'platforms.required' =>
                'Debes seleccionar al menos una plataforma.',

            'platforms.min' =>
                'Debes seleccionar al menos una plataforma.',

        ];
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