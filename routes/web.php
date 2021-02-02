<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/',

	function () use ($router) {
		return $router->app->version();
	});

$router->group(['prefix'              => 'api'], function () use ($router) {
		$router->post('searchuser', ['uses' => 'UserController@getUserRequest']);

		$router->get('listusers', ['uses' => 'UserController@showAllUsers']);

		$router->post('createuser', ['uses' => 'UserController@create']);

		$router->get('deleteuser/{id}', ['uses' => 'UserController@delete']);

		$router->post('updateuser', ['uses' => 'UserController@update']);

		$router->get('togglemode/{id}', ['uses' => 'UserController@toggleDarkmode']);
	});
