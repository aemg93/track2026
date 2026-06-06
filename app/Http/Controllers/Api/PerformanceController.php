<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Performance::query();

        if ($user && method_exists($user, 'hasRole')) {

            if ($user->hasRole('Admin')) {
                $query->where('studio_id', $user->studio_id);
            }

            if ($user->hasRole('Performance')) {
                $query->where('user_id', $user->id);
            }
        }

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

        $sortBy = $request->get('sortBy', 'ranking_score');
        $order = $request->get('order', 'desc');
        $limit = $request->get('limit', 10);

        $performances = $query
            ->with([
                'earnings',
                'bonuses',
                'penalties',
                'split',
                'user',
                'studio'
            ])
            ->orderBy($sortBy, $order)
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $performances->items(),
            'meta' => [
                'page' => $performances->currentPage(),
                'pages' => $performances->lastPage(),
                'total' => $performances->total(),
            ]
        ]);
    }

    
    public function store(Request $request)
    {
        $user = $request->user();

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

            'profile_photo' => 'nullable|string|max:2048',

            'active' => 'nullable|boolean',
            'hours_streamed' => 'nullable|integer',
            'ranking_score' => 'nullable|numeric',
        ]);

        if ($user) {
            $data['studio_id'] = $data['studio_id'] ?? $user->studio_id;
            $data['user_id'] = $data['user_id'] ?? $user->id;
        }

        $data['active'] = $data['active'] ?? true;
        $data['hours_streamed'] = $data['hours_streamed'] ?? 0;
        $data['ranking_score'] = $data['ranking_score'] ?? 0;

        $performance = Performance::create($data);

        return response()->json([
            'success' => true,
            'data' => $performance->load([
                'earnings',
                'bonuses',
                'penalties',
                'split',
                'user',
                'studio'
            ])
        ], 201);
    }

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

        if ($user && method_exists($user, 'hasRole')) {

            if (
                $user->hasRole('Admin') &&
                $performance->studio_id != $user->studio_id
            ) {
                abort(403);
            }

            if (
                $user->hasRole('Performance') &&
                $performance->user_id != $user->id
            ) {
                abort(403);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $performance
        ]);
    }

    public function update(Request $request, $id)
    {
        $performance = Performance::findOrFail($id);

        $data = $request->validate([
            'studio_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',

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

            'profile_photo' => 'nullable|string|max:2048',

            'active' => 'nullable|boolean',

            'hours_streamed' => 'nullable|integer',
            'ranking_score' => 'nullable|numeric',
        ]);

        if (
            array_key_exists('profile_photo', $data) &&
            trim((string) $data['profile_photo']) === ''
        ) {
            unset($data['profile_photo']);
        }

        $performance->update($data);

        return response()->json([
            'success' => true,
            'data' => $performance->fresh()->load([
                'earnings',
                'bonuses',
                'penalties',
                'split',
                'user',
                'studio'
            ])
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