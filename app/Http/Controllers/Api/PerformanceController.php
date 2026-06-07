<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
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
            $query->where('active', $request->active);
        }

        $limit = (int) $request->get('limit', 10);

        $data = $query
            ->with(['earnings', 'bonuses', 'penalties', 'split', 'user', 'studio'])
            ->orderBy('ranking_score', 'desc')
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'studio_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'nickname' => 'nullable|string',
            'email' => 'required|email|unique:performances,email',
            'phone' => 'nullable|string',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'document_type' => 'nullable|string',
            'document_number' => 'nullable|string',
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
            'data' => $performance
        ], 201);
    }

    public function show($id)
    {
        $performance = Performance::with([
            'earnings',
            'bonuses',
            'penalties',
            'split',
            'user',
            'studio'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $performance
        ]);
    }

    public function update(Request $request, $id)
    {
        $performance = Performance::findOrFail($id);

        $data = $request->validate([
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'active' => 'nullable|boolean',
            'hours_streamed' => 'nullable|integer',
            'ranking_score' => 'nullable|numeric',
        ]);

        $performance->update($data);

        return response()->json([
            'success' => true,
            'data' => $performance->fresh()
        ]);
    }

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