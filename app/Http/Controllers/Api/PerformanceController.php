<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | RELATIONS CENTRALIZED
    |--------------------------------------------------------------------------
    */

    private function relations()
    {
        return [
            'earnings',
            'bonuses',
            'penalties',
            'split',
            'user',
            'studio'
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Performance::query();

        // SEARCH
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // FILTER ACTIVE
        if ($request->filled('active')) {
            $query->where('active', filter_var($request->active, FILTER_VALIDATE_BOOLEAN));
        }

        // SORT SAFETY
        $allowedSorts = ['ranking_score', 'created_at', 'hours_streamed'];
        $sortBy = $request->get('sortBy', 'ranking_score');

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'ranking_score';
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
            'ranking_score' => 'nullable|numeric',
        ]);

        $data['active'] = $data['active'] ?? true;
        $data['hours_streamed'] = $data['hours_streamed'] ?? 0;
        $data['ranking_score'] = $data['ranking_score'] ?? 0;

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
            'ranking_score' => 'nullable|numeric',
        ]);

        $performance->update($data);

        return response()->json([
            'success' => true,
            'data' => $performance->fresh()->load($this->relations())
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
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
}