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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/insert', 'userController@store');
Route::get('/select', 'userController@index');
Route::get('/select2', 'userController@show');
Route::get('/check', 'userController@pass');

Route::put('/update', 'userController@update');
Route::delete('/delete', 'userController@delete');


