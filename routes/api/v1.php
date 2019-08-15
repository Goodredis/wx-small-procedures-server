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
    $api->get('attendances/item', 'AttendanceController@item');
    $api->get('attendances/list', 'AttendanceController@list');
    $api->get('attendances/export', 'AttendanceController@export');
    $api->resources([ 'attendances' => 'AttendanceController' ]);
    $api->resources([ 'test' => 'TestController' ]);
    //合同框架的批量操作
    $api->post('framworkContracts/batch', 'FrameworkController@batch');
    //合同框架的导入
    $api->post('framworkContracts/content', 'FrameworkController@import');
    //合同框架的增删改查
    $api->resources([ 'framworkContracts' => 'FrameworkController' ]);
    //合同框架详情的导入
    $api->post('framworkContractDetails/content', 'FrameworkdetailsController@import');
    //合同框架详情的增删改查
    $api->resources([ 'framworkContractDetails' => 'FrameworkdetailsController' ]);
    //厂商的增删改查
    $api->resources([ 'suppliers' => 'SupplierController' ]);
});
