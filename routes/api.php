<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('index', 'TaskController@index');
Route::post('store', 'TaskController@store');
Route::post('task-done/{id}', 'TaskController@taskDone');
Route::post('task-edit/{id}', 'TaskController@edit');
Route::post('task-update/{id}', 'TaskController@update');
Route::post('task-delete/{id}', 'TaskController@delete');