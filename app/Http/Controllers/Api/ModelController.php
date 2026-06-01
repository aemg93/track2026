<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PerformanceResource;
use App\Models\Performance;
use App\Services\PerformanceFinancialService;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    public function index(Request $request, PerformanceFinancialService $service)
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
            $query->where('active', $request->status === 'active');
        }

        $sortBy = $request->get('sortBy', 'ranking_score');
        $order = $request->get('order', 'desc');

        $allowedSorts = [
            'ranking_score',
            'hours_streamed',
            'created_at',
            'first_name'
        ];

        if (!in_array($sortBy, $allowedSorts)) {
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

        $models->getCollection()->transform(function ($model) use ($service) {
            $model->financials = $service->calculate($model);
            return $model;
        });

        return response()->json([
            'success' => true,
            'data' => PerformanceResource::collection($models->items()),
            'meta' => [
                'page' => $models->currentPage(),
                'pages' => $models->lastPage(),
                'total' => $models->total(),
            ]
        ]);
    }

    public function show($id, PerformanceFinancialService $service)
    {
        $model = Performance::with([
            'earnings',
            'bonuses',
            'penalties',
            'split',
            'user',
            'studio'
        ])->findOrFail($id);

        $model->financials = $service->calculate($model);

        return response()->json([
            'success' => true,
            'data' => new PerformanceResource($model)
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'studio_id' => 'required|integer',
            'user_id' => 'nullable|integer',

            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'nickname' => 'nullable|string',

            'email' => 'required|email',
            'phone' => 'nullable|string',

            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',

            'document_type' => 'nullable|string',
            'document_number' => 'nullable|string',

            'birth_date' => 'required|date',
            'profile_photo' => 'nullable|string',

            'active' => 'boolean',
            'hours_streamed' => 'nullable|integer',
            'ranking_score' => 'nullable|numeric',
        ]);

        $model = Performance::create($data);

        return response()->json([
            'success' => true,
            'data' => new PerformanceResource($model)
        ], 201);
    }
}