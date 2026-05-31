<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Performance::query();

        // SEARCH
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // STATUS
        if ($request->status !== null && $request->status !== '') {
            $query->where('active', $request->status === 'active');
        }

        // SORT
        $sortBy = $request->sortBy ?? 'ranking_score';
        $order = $request->order ?? 'desc';

        // PAGINATION
        $limit = $request->limit ?? 10;

        $models = $query
            ->orderBy($sortBy, $order)
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $models->items(),
            'meta' => [
                'page' => $models->currentPage(),
                'pages' => $models->lastPage(),
                'total' => $models->total(),
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DETALLE
    |--------------------------------------------------------------------------
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
            'data' => $model
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'studio_id' => 'required|integer',
            'user_id' => 'nullable|integer',
            'active' => 'boolean',
        ]);

        $model = Performance::create([
            'name' => $data['name'],
            'studio_id' => $data['studio_id'],
            'user_id' => $data['user_id'] ?? null,
            'active' => $data['active'] ?? true,
            'hours_streamed' => 0,
            'ranking_score' => 0,
        ]);

        return response()->json([
            'success' => true,
            'data' => $model
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $model = Performance::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'active' => 'sometimes|boolean',
            'hours_streamed' => 'sometimes|integer',
            'ranking_score' => 'sometimes|numeric',
        ]);

        $model->update($data);

        return response()->json([
            'success' => true,
            'data' => $model
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
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