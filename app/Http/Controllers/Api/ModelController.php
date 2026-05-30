<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    /**
     * LISTADO CON FILTROS + PAGINACIÓN
     */
    public function index(Request $request)
    {
        $query = Performance::query();

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */
        if ($request->status !== null && $request->status !== '') {
            $query->where('active', $request->status === 'active');
        }

        /*
        |--------------------------------------------------------------------------
        | SORT
        |--------------------------------------------------------------------------
        */
        $sortBy = $request->sortBy ?? 'ranking_score';
        $order = $request->order ?? 'desc';

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */
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
                'total' => $models->total()
            ]
        ]);
    }

    /**
     * DETALLE DE MODELO
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
}