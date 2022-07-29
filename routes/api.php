<?php

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['namespace' => 'App\Http\Controllers\Api', 'prefix' => 'tasks'], function() {
    Route::get('/', 'TaskController@index');
    Route::get('/findByFilter', 'TaskController@findByFilter');
    Route::get('/{id}', 'TaskController@show');
    Route::post('/', 'TaskController@store');
    Route::put('/{id}', 'TaskController@update');
    Route::delete('/{id}', 'TaskController@destroy');
});
