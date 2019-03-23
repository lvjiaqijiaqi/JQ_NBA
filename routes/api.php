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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api'
] , function($api) {
    $api->get('socials/{social_type}/authorizations', function() {
        return response('this is version v1');
    });
    $api->get('players', 'GameController@index')
    ->name('api.game.index');

    $api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
    ->name('api.socials.authorizations.store');

});
