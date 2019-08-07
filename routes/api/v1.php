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
    $api->resources([ 'test' => 'TestController' ]);
    $api->resources([ 'attendance' => 'AttendanceController' ]);
    $api->resources([ 'framwork' => 'FramworkController' ]);
    $api->resources([ 'framworkdetails' => 'FramworkdetailsController' ]);
    $api->resources([ 'supplier' => 'SupplierController' ]);
});
