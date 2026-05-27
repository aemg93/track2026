<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WEB ROUTES (SOLO FRONTEND)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});