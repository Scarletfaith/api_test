<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;

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

//Route::resource('tasks', TaskController::class);

Route::group(['namespace' => 'App\Http\Controllers\Api', 'prefix' => 'tasks'], function() {
    Route::get('/', 'TaskController@index');
    Route::get('/{id}', 'TaskController@show');
    Route::post('/findByFilter', 'TaskController@findByFilter');
    Route::post('/', 'TaskController@store');
    Route::put('/{id}', 'TaskController@update');
});
