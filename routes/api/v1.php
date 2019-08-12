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
    // Auth
    $api->post('attendance', 'AttendanceController@store');
    $api->get('attendance', 'AttendanceController@index');
    $api->get('attendance/{uid}/{date}', 'AttendanceController@show');
    $api->put('attendance/{id}', 'AttendanceController@update');
    $api->delete('attendance/{id}', 'AttendanceController@destroy');

    $api->resources([ 'test' => 'TestController' ]);
    //合同框架的批量删除
    $api->post('frameworkContracts/batchdeletion', 'FrameworkController@destroymany');
    //合同框架的增删改查
    $api->resources([ 'frameworkContracts' => 'FrameworkController' ]);
    $api->resources([ 'frameworkdetailsContracts' => 'FrameworkdetailsController' ]);
    //厂商的增删改查
    $api->resources([ 'supplier' => 'SupplierController' ]);
});
