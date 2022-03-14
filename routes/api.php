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
Route::get('/codeResponse', 'App\Http\Controllers\CodeResponseController@index'); //Traer datos para Codigo de Respuesta
Route::get('/entryMode', 'App\Http\Controllers\EntryModeController@index'); //Traer datos para Entry Mode
//RUTAS TOKEN C4
Route::get('/tokenC4', 'App\Http\Controllers\TokenC4Controller@index'); //Traer datos para tabla principal
Route::get('/tokenC4DataTable', 'App\Http\Controllers\TokenC4Controller@getDataTable'); //Traer datos no filtrados
Route::post('/tokenC4Filter', 'App\Http\Controllers\TokenC4Controller@getDataTableFilter'); //Traer datos para tabla filtro
