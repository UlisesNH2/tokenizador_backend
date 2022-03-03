<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test','App\Http\Controllers\TestController@index'); //Traer los datos para dashboard
Route::get('/kq2', 'App\Http\Controllers\Kq2Controller@index'); //Traer los datos para Medio-Acceso (gráficos)
Route::get('/kq2G', 'App\Http\Controllers\Kq2GraphsController@index'); //Traer los datos para Medio-Acceso (gráficos)

