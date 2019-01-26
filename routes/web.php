<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers\V1'], function($api){

    $api->group(['middleware' => ['checkuserauth']], function($api){
        
        //档案
        $api->post('/archive', ['as'=>'vr.archive.addarchive', 'uses'=>'ArchiveController@AddArchive']);
        $api->put('/archive/{id}', ['as'=>'vr.archive.updatearchive', 'uses'=>'ArchiveController@UpdateArchive']);
        $api->get('/archive/{id}', ['as'=>'vr.archive.getarchive', 'uses'=>'ArchiveController@GetArchiveById']);
        
    });


});