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

Route::post('/userLogin', 'App\Http\Controllers\UserController@findUSer');

Route::get('/dashboard','App\Http\Controllers\DashboardController@index'); //Traer los datos para dashboard
Route::post('/dashboardFilter', 'App\Http\Controllers\DashboardController@filterDashboard');//Filtar datos para dashboard
//PRINCIPALES FILTROS
Route::get('/kq2', 'App\Http\Controllers\Kq2Controller@index'); //Traer los datos para Medio-Acceso (gráficos)
Route::post('/kq2Filter', 'App\Http\Controllers\Kq2Controller@filterKq2');

Route::get('/codeResponse', 'App\Http\Controllers\CodeResponseController@index'); //Traer datos para Codigo de Respuesta
Route::post('/codeResponseFilter', 'App\Http\Controllers\CodeResponseController@filterCodeResponse');

Route::get('/entryMode', 'App\Http\Controllers\EntryModeController@index'); //Traer datos para Entry Mode
Route::post('/entryModeFilter', 'App\Http\Controllers\EntryModeController@filterEntryMode');

//RUTAS TOKEN C4
Route::get('/tokenC4', 'App\Http\Controllers\TokenC4Controller@index'); //Traer datos para tabla principal
Route::get('/tokenC4DataTable', 'App\Http\Controllers\TokenC4Controller@getDataTable'); //Traer datos no filtrados
Route::post('/tokenC4Filter', 'App\Http\Controllers\TokenC4Controller@getDataTableFilter'); //Datos para tabla filtro

//RUTAS TOKEN C0
Route::get('/tokenC0', 'App\Http\Controllers\TokenC0Controller@index');//Traer datos para tabla principal
Route::post('/tokenC0Filter', 'App\Http\Controllers\TokenC0Controller@getDataTableFilter'); //Datos para el filtro

//RUTAS TOKEN B3
Route::get('/tokenB3', 'App\Http\Controllers\TokenB3Controller@index'); //Traer datos para la tabla principal
Route::post('/tokenB3Filter', 'App\Http\Controllers\TokenB3Controller@getDataTableFilter');//Datos para el filtro

//RUTAS TOKEN B4
Route::get('/tokenB4', 'App\Http\Controllers\TokenB4Controller@index');//Traeer datos para tabla principal
Route::post('/tokenB4Filter', 'App\Http\Controllers\TokenB4Controller@getDataTableFilter');//Datos para el filtro

//RUTAS TOKEN B2
Route::get('/tokenB2', 'App\Http\Controllers\TokenB2Controller@index');//Traer datos para tabla principal
Route::post('/tokenB2Filter', 'App\Http\Controllers\TokenB2Controller@getDataTableFilter');//Datos para el filtro
