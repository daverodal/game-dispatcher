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

Route::middleware('auth:api')->group(function(){

        Route::post('area-maps', 'AreaMapsController@store');
        Route::get('area-maps', 'AreaMapsController@index');
        Route::get('area-maps/{id}', 'AreaMapsController@show');
        Route::put('area-maps/{id}', 'AreaMapsController@update');

});

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:api');
