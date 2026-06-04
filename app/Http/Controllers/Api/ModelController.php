<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PerformanceResource;
use App\Models\Performance;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    /**
     * LISTADO
     */
    public function index(Request $request)
    {
        $query = Performance::query();

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('nickname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {

            $query->where(
                'active',
                $request->status === 'active'
            );
        }

        $sortBy = $request->get('sortBy', 'ranking_score');
        $order = $request->get('order', 'desc');

        $allowedSorts = [
            'ranking_score',
            'hours_streamed',
            'created_at',
            'first_name',
            'last_name'
        ];

        if (! in_array($sortBy, $allowedSorts)) {
            $sortBy = 'ranking_score';
        }

        $limit = $request->get('limit', 10);

        $models = $query
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
            'data' => PerformanceResource::collection(
                $models->items()
            ),
            'meta' => [
                'page' => $models->currentPage(),
                'pages' => $models->lastPage(),
                'total' => $models->total(),
            ]
        ]);
    }

    /**
     * SHOW
     */
    public function show($id)
    {
        $model = Performance::with([
            'earnings',
            'bonuses',
            'penalties',
            'split',
            'user',
            'studio'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new PerformanceResource($model)
        ]);
    }

    /**
     * CREATE
     */
    public function store(Request $request)
    {
        $data = $request->validate([

            'studio_id' => 'required|integer',
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

            'active' => 'boolean',

            'hours_streamed' => 'nullable|integer',
            'ranking_score' => 'nullable|numeric',
        ]);

        $data['active'] = $data['active'] ?? true;
        $data['hours_streamed'] = $data['hours_streamed'] ?? 0;
        $data['ranking_score'] = $data['ranking_score'] ?? 0;

        $model = Performance::create($data);

        return response()->json([
            'success' => true,
            'data' => new PerformanceResource(
                $model->load([
                    'earnings',
                    'bonuses',
                    'penalties',
                    'split',
                    'user',
                    'studio'
                ])
            )
        ], 201);
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $model = Performance::findOrFail($id);

        $data = $request->validate([

            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'nickname' => 'sometimes|nullable|string|max:255',

            'email' => 'sometimes|email',
            'phone' => 'sometimes|nullable|string|max:255',

            'country' => 'sometimes|nullable|string|max:255',
            'city' => 'sometimes|nullable|string|max:255',
            'address' => 'sometimes|nullable|string',

            'document_type' => 'sometimes|nullable|string|max:255',
            'document_number' => 'sometimes|nullable|string|max:255',

            'birth_date' => 'sometimes|date',

            'profile_photo' => 'sometimes|nullable|string',

            'active' => 'sometimes|boolean',

            'hours_streamed' => 'sometimes|integer',
            'ranking_score' => 'sometimes|numeric',
        ]);

        $model->update($data);

        return response()->json([
            'success' => true,
            'data' => new PerformanceResource(
                $model->fresh()->load([
                    'earnings',
                    'bonuses',
                    'penalties',
                    'split',
                    'user',
                    'studio'
                ])
            )
        ]);
    }

    /**
     * DELETE
     */
    public function destroy($id)
    {
        $model = Performance::findOrFail($id);

        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Modelo eliminado correctamente'
        ]);
    }
}