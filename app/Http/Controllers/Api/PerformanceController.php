<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use Illuminate\Http\Request;
use App\Services\PerformanceAnalyticsService;

class PerformanceController extends Controller
{
    private function relations()
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

    /*
    |--------------------------------------------------------------------------
    | LIST
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Performance::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('nickname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('active')) {
            $query->where(
                'active',
                filter_var($request->active, FILTER_VALIDATE_BOOLEAN)
            );
        }

        $allowedSorts = ['created_at', 'hours_streamed'];
        $sortBy = $request->get('sortBy', 'created_at');

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $order = $request->get('order', 'desc');
        $limit = (int) $request->get('limit', 10);

        $data = $query
            ->with($this->relations())
            ->orderBy($sortBy, $order)
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'meta' => [
                'page' => $data->currentPage(),
                'pages' => $data->lastPage(),
                'total' => $data->total(),
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $data = $request->validate([
            'studio_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:performances,email',
            'phone' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'document_type' => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'birth_date' => 'required|date',
            'profile_photo' => 'nullable|string',
            'active' => 'nullable|boolean',
            'hours_streamed' => 'nullable|integer',
        ]);

        $data['active'] = $data['active'] ?? true;
        $data['hours_streamed'] = $data['hours_streamed'] ?? 0;

        $performance = Performance::create($data);

        return response()->json([
            'success' => true,
            'data' => $performance->load($this->relations())
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $performance = Performance::with($this->relations())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $performance
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $performance = Performance::findOrFail($id);

        $data = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'document_type' => 'nullable|string|max:255',
            'document_number' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'profile_photo' => 'nullable|string',
            'active' => 'nullable|boolean',
            'hours_streamed' => 'nullable|integer',
        ]);

        $performance->update($data);

        return response()->json([
            'success' => true,
            'data' => $performance->fresh()->load($this->relations())
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $performance = Performance::findOrFail($id);
        $performance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ANALYTICS
    |--------------------------------------------------------------------------
    */
    public function analytics($id)
    {
        $performance = Performance::with('platforms')->findOrFail($id);

        $from = request('from');
        $to = request('to');

        $service = app(PerformanceAnalyticsService::class);

        $data = $service->summary($performance, $from, $to);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LEADERBOARD (FIXED)
    |--------------------------------------------------------------------------
    */
    public function leaderboard(Request $request)
    {
        $limit = (int) $request->get('limit', 20);

        $performances = Performance::query()
            ->select([
                'id',
                'first_name',
                'last_name',
                'nickname',
                'profile_photo',
                'hours_streamed',
                'active',
            ])
            ->where('active', true)
            ->orderByDesc('hours_streamed')
            ->limit($limit)
            ->get()
            ->map(function ($p) {
                $p->ranking_score = 0; // placeholder seguro
                return $p;
            });

        return response()->json([
            'success' => true,
            'data' => $performances
        ]);
    }
}