<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EarningService;
use Illuminate\Http\Request;


class EarningController extends Controller
{

    public function index(
        Request $request,
        EarningService $service
    )
    {

        return response()->json([

            'success'=>true,

            'data'=>$service->list(
                $request->user()
            )

        ]);

    }

}