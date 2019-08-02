<?php
use Laravel\Lumen\Routing\router;

/*
|--------------------------------------------------------------------------
| Application routers
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routers for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
/** @var \Laravel\Lumen\Routing\router $router */
$router->get('/', function () use ($router) {
    return $router->app->version();
});