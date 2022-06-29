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
//USUARIO
Route::post('/userLogin', 'App\Http\Controllers\UserController@findUSer'); //Buscar usuario en la base de datos para login
Route::post('/users', 'App\Http\Controllers\UserController@index');//Traer a todos los usuarios
Route::post('/createUser', 'App\Http\Controllers\UserController@createUser'); //Crear usuario 
Route::post('/updateUser', 'App\Http\Controllers\UserController@updateUser'); //Actualizar usuario
Route::post('/deleteUser', 'App\Http\Controllers\UserController@deleteUser'); //Eliminar usuario

Route::post('/dashboard','App\Http\Controllers\DashboardController@index'); //Traer los datos para dashboard

//PRINCIPALES FILTROS
Route::get('/kq2', 'App\Http\Controllers\Kq2Controller@index'); //Traer los datos para Medio-Acceso (gráficos)
Route::post('/kq2Filter', 'App\Http\Controllers\Kq2Controller@filterKq2');

Route::get('/codeResponse', 'App\Http\Controllers\CodeResponseController@index'); //Traer datos para Codigo de Respuesta
Route::post('/codeResponseFilter', 'App\Http\Controllers\CodeResponseController@filterCodeResponse');

Route::get('/entryMode', 'App\Http\Controllers\EntryModeController@index'); //Traer datos para Entry Mode
Route::post('/entryModeFilter', 'App\Http\Controllers\EntryModeController@filterEntryMode');

//FILTROS TERMINALES
Route::post('/terminalFilter', 'App\Http\Controllers\TerminalController@index');
Route::get('/getCatalogs', 'App\Http\Controllers\TerminalController@getCatalogs');

//RUTAS TOKEN C4
Route::post('/tokenC4', 'App\Http\Controllers\TokenC4Controller@index'); //Datos para el formulario
Route::post('/tokenC4Filter/main', 'App\Http\Controllers\TokenC4Controller@getTableFilter'); //Datos para tabla del Token

//RUTAS TOKEN C0
Route::post('/tokenC0', 'App\Http\Controllers\TokenC0Controller@index');//Traer datos para tabla principal
Route::post('/tokenC0Filter/main', 'App\Http\Controllers\TokenC0Controller@getDataTableFilter'); //Datos para el filtro

//RUTAS TOKEN B3
Route::post('/tokenB3', 'App\Http\Controllers\TokenB3Controller@index'); //Traer datos para la tabla principal
Route::post('/tokenB3Filter/main', 'App\Http\Controllers\TokenB3Controller@getDataTableFilter');//Datos para el filtro

//RUTAS TOKEN B4
Route::post('/tokenB4', 'App\Http\Controllers\TokenB4Controller@index');//Traeer datos para tabla principal
Route::post('/tokenB4Filter/main', 'App\Http\Controllers\TokenB4Controller@getDataTableFilter');//Datos para el filtro

//RUTAS TOKEN B2
Route::post('/tokenB2', 'App\Http\Controllers\TokenB2Controller@index');//Traer datos para tabla principal
Route::post('/tokenB2Filter/main', 'App\Http\Controllers\TokenB2Controller@getDataTableFilter');//Datos para el filtro

//RUTAS TOKEN B5
Route::post('/tokenB5', 'App\Http\Controllers\TokenB5Controller@index');//Traer datos para tabla principal
Route::post('/tokenB5Filter/main', 'App\Http\Controllers\TokenB5Controller@getDataTableFilter'); //Datos para el filtro

//RUTAS TOKEN B6
Route::post('/tokenB6', 'App\Http\Controllers\TokenB6Controller@index');//Traer datos para tabla principal
Route::post('/tokenB6Filter/main', 'App\Http\Controllers\TokenB6Controller@getDataTableFilter'); //Datos para el filtro
