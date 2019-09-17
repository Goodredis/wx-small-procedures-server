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
$api = app('Dingo\Api\Routing\Router');
// v1 version API
// add in header    Accept:application/vnd.lumen.v1+json
$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1',
    // 'middleware' => [
    //     'cors',
    //     'serializer',
    //     'api.throttle',
    // ],
    // each route have a limit of 20 of 1 minutes
    'limit' => 20, 'expires' => 1,
], function ($api) {
    // 项目初始化测试
    $api->resources([ 'test' => 'TestController' ]);

    // 登录认证，创建token 
    $api->post('authorizations', 'AuthController@store');
    // 用户登出，销毁token 
    $api->delete('authorizations/current', 'AuthController@delete');
    // 刷新token 
    $api->put('authorizations/current', 'AuthController@update');

    $api->group(['middleware' => 'auth'], function ($api) {
        // 项目经理的增删改
        $api->resources([ 'users' => 'UserController' ]);
    });

    // 用户预约
    $api->resources([ 'order' => 'OrderController' ]);
});
