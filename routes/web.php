<?php

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

Route::get('/', 'FrontController@anyIndex');

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

Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');

Route::group(['middleware' => ['auth']], function () {
    Route::group(['prefix' => 'rest'], function () {
        Route::get('hexStrs/{id}', 'MapsController@getHexstrs');
        Route::put('hexStrs/{id}', 'MapsController@putHexstrs');
        Route::post('hexStrs', 'MapsController@postHexstrs');
        Route::delete('hexStrs/{id}', 'MapsController@deleteHexstrs');
        Route::get('cloneFile/{id}', 'MapsController@cloneFile');
        Route::get('maps', 'MapsController@getMaps');
        Route::post('maps', 'MapsController@postMaps');
        Route::put('maps/{id}', 'MapsController@putMaps');
        Route::delete('maps/{id}', 'MapsController@deleteMaps');
    });

    Route::group(['prefix' => 'admin'], function(){
        Route::any('/', 'AdminController@anyIndex');
        Route::get('logins', 'AdminController@getLogins');
        Route::get('users', 'AdminController@getUsers');
        Route::get('games', 'AdminController@getGames');
        Route::get('allgames', 'AdminController@getAllgames');
        Route::get('delete-bug/{id}', 'AdminController@getDeleteBug');
        Route::get('allbugs', 'AdminController@getAllbugs');
        Route::get('add-game', 'AdminController@getAddGame');
        Route::get('delete-game/{id}', 'AdminController@getDeleteGame');
        Route::get('delete-game-type', 'AdminController@getDeleteGameType');
    });

    Route::group(['prefix'=> 'wargame'], function(){


        Route::get('terrainInit/{id}/{jd}/{kd}', 'WargameController@terrainInit');

        Route::get('play/{wargame?}', 'WargameController@getPlay');
        Route::any('fetch-lobby/{lastSeq?}', 'WargameController@anyFetchLobby');
        Route::get('setplay/{id}', 'WargameController@getSetplay');
        Route::get('test-analytics', 'WargameController@getTestAnalytics');
        Route::get('clone-scenario/{dir?}/{genre?}/{game?}/{scenario?}', 'WargameController@getCloneScenario');
        Route::get('scenario-delete/{id}', 'WargameController@getScenarioDelete');
        Route::get('unattached-game/{dir?}/{genre?}/{game?}/{scenario?}', 'WargameController@getUnattachedGame');
        Route::get('scenario-edit/{id}', 'WargameController@getScenarioEdit');
        Route::get('scenario-vue-edit/{id}', 'WargameController@getScenarioVueEdit');
        Route::get('apply-deploys/{wargame}/{deploy}', 'WargameController@getApplyDeploys');
        Route::get('list-deploys/{wargame}', 'WargameController@getListDeploys');
        Route::get('make-new-game/{historyFile}', 'WargameController@getMakeNewGame');
        Route::post('create-wargame/{type}/{game}/{scenario}', 'WargameController@postCreateWargame');
        Route::get('leave-game', 'WargameController@getLeaveGame');
        Route::get('delete-game/{gameName}', 'WargameController@getDeleteGame');
        Route::get('unit-init/{game}/{arg?}', 'WargameController@getUnitInit');
        Route::get('play-as/{game}/{docId}', 'WargameController@getPlayAs');
        Route::get('enter-hotseat/{newWargame}', 'WargameController@getEnterHotseat');
        Route::get('enter-multi/{wargame?}/{playerOne?}/{playerTwo?}/{visibility?}/{playerThree?}/{playerFour?}', 'WargameController@getEnterMulti');
        Route::get('add-friend/{id}', 'WargameController@getAddFriend');
        Route::get('remove-friend/{id}', 'WargameController@getRemoveFriend');
        Route::get('change-wargame/{newWargame?}', 'WargameController@getChangeWargame');
        Route::get('fetch/{wargame}/{lastSeq?}', 'WargameController@getFetch');
        Route::post('poke', 'WargameController@postPoke');
        Route::get('make-public/{docId}', 'WargameController@getMakePublic');
        Route::get('make-private/{docId}', 'WargameController@getMakePrivate');
        Route::get('custom-scenario/{id}', 'WargameController@getCustomScenario');
        Route::put('custom-scenario/{id}/{scenario}', 'WargameController@putCustomScenario');


    });
    /*
        getIndex
    //getPlay
    //anyFetchLobby
    //getSetplay
    //getTestAnalytics
    //getCloneScenario
    //getScenarioDelete
    //getUnattachedGame
    //getScenarioEdit
    //getApplyDeploys
    //getListDeploys
    //getMakeNewGame
    //postCreateWargame
    //getLeaveGame
    //getDeleteGame
    //getUnitInit
    //getPlayAs
    //getEnterHotseat
    //getEnterMulti
    //getAddFriend
    //getRemoveFriend
    //enterHotseat
    //getChangeWargame
    //getFetch
    //postPoke
    //terrainInit
    //getMakePublic
    //getMakePrivate
    //getCustomScenario
    //putCustomScenario
    */
    Route::group(['prefix' => 'messages'], function () {
        Route::get('/', ['as' => 'messages', 'uses' => 'MessagesController@index']);
        Route::get('create', ['as' => 'messages.create', 'uses' => 'MessagesController@create']);
        Route::post('/', ['as' => 'messages.store', 'uses' => 'MessagesController@store']);
        Route::get('{id}', ['as' => 'messages.show', 'uses' => 'MessagesController@show']);
        Route::post('{id}', ['as' => 'messages.update', 'uses' => 'MessagesController@update']);
    });
});


//Route::get('/register', function () {
//    return redirect('/');
//});