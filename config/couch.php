<?php
/**
 * Created by PhpStorm.
 * User: david_rodal
 * Date: 1/4/16
 * Time: 12:59 PM
 */

return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
    */
    'hostname' => '127.0.0.1',
    'port'=>5984,
    'username'=>env('COUCH_USERNAME', ''),
    'password'=>env('COUCH_PASSWORD', ''),
    'rest'=> env('COUCH_REST', 'rest'),
    'mydatabase'=> env('COUCH_MYDATABASE', 'mydatabase'),
    'games'=> env('COUCH_GAMES', 'mydatabase'),
    'users'=> env('COUCH_USERS','users'),
    'analytics' => env('COUCH_ANALYTICS','analytics'),
    'params' => env('COUCH_PARAMS','params'),
    'terrain' => env('COUCH_TERRAIN','terrain'),
    'clicks' => env('COUCH_CLICKS', 'clicks')
];