<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    /**
     * Mostrar la lista de plataformas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $platforms = Platform::select('id', 'name', 'type')
            ->orderBy('name')
            ->get();

        return response()->json($platforms);
    }
}