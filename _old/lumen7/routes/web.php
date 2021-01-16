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
  //return $router->app->version();
  return '<pre>'.print_r(config(),true).'</pre>';
});

$router->group(['prefix' => 'api/auth'], function () use ($router) 
{
	$router->post('register', 'AuthController@register');
	$router->post('login'	, 'AuthController@login');
});

$router->group(['prefix' => 'api/test'], function () use ($router) 
{
	$router->get('all'		, 'AuthController@allAccess');
});
