<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    /**
     * LISTADO SAAS
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Performance::query();

        // SCOPING
        if ($user->hasRole('Admin')) {
            $query->where('studio_id', $user->studio_id);
        }

        if ($user->hasRole('Performance')) {
            $query->where('user_id', $user->id);
        }

        // SEARCH (FIX REAL)
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('nickname', 'like', "%{$request->search}%");
            });
        }

        // ACTIVE FILTER
        if ($request->filled('active')) {
            $query->where('active', $request->active);
        }

        // SORT
        $sortBy = $request->sortBy ?? 'ranking_score';
        $order = $request->order ?? 'desc';
        $limit = $request->limit ?? 10;

        $performances = $query
            ->orderBy($sortBy, $order)
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $performances->items(),
            'meta' => [
                'page' => $performances->currentPage(),
                'pages' => $performances->lastPage(),
                'total' => $performances->total()
            ]
        ]);
    }

    /**
     * CREATE (100% ALINEADO A MIGRATION)
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'studio_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',

            'first_name' => 'required|string',
            'last_name' => 'required|string',

            'email' => 'required|email|unique:performances,email',
            'phone' => 'nullable|string',

            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',

            'document_type' => 'nullable|string',
            'document_number' => 'nullable|string',

            'birth_date' => 'required|date',

            'active' => 'boolean',
            'hours_streamed' => 'nullable|numeric',
            'ranking_score' => 'nullable|numeric',
        ]);

        // FIX NAME
        $data['name'] = trim($data['first_name'] . ' ' . $data['last_name']);

        // DEFAULTS SEGURAS
        $data['studio_id'] = $data['studio_id'] ?? $user->studio_id;
        $data['user_id'] = $data['user_id'] ?? $user->id;

        $performance = Performance::create($data);

        return response()->json([
            'success' => true,
            'data' => $performance
        ], 201);
    }

    /**
     * SHOW
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $performance = Performance::with([
            'earnings',
            'bonuses',
            'penalties',
            'split',
            'user',
            'studio'
        ])->findOrFail($id);

        if ($user->hasRole('Admin') && $performance->studio_id !== $user->studio_id) {
            abort(403);
        }

        if ($user->hasRole('Performance') && $performance->user_id !== $user->id) {
            abort(403);
        }

        return response()->json([
            'success' => true,
            'data' => $performance
        ]);
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $performance = Performance::findOrFail($id);

        $data = $request->validate([
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'email' => 'sometimes|email',
            'active' => 'sometimes|boolean',
            'hours_streamed' => 'sometimes|numeric',
            'ranking_score' => 'sometimes|numeric',
        ]);

        if (isset($data['first_name']) || isset($data['last_name'])) {
            $first = $data['first_name'] ?? $performance->first_name;
            $last = $data['last_name'] ?? $performance->last_name;

            $data['name'] = trim($first . ' ' . $last);
        }

        $performance->update($data);

        return response()->json([
            'success' => true,
            'data' => $performance
        ]);
    }

    /**
     * DELETE
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