<?php
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return redirect('home');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
Route::group(['middleware' => ['web']], function () {
    Route::auth();
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('rest/hexStrs/{id}','MapsController@getHexstrs');
    Route::put('rest/hexStrs/{id}','MapsController@putHexstrs');
    Route::post('rest/hexStrs','MapsController@postHexstrs');
    Route::delete('rest/hexStrs/{id}','MapsController@deleteHexstrs');

    Route::controller('rest','MapsController');
});

Route::group(['middleware' => 'web'], function () {
    Route::get('/home', 'HomeController@index');
});

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/home', 'HomeController@index');
});
