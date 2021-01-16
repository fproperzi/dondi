<?php
use Illuminate\Support\Facades\Mail;
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

$router->get('/', function () use ($router) {
    return $router->app->version().'  '.trans('welcome');

});
$router->get('/mail', function () use ($router) {
	$message ="hallo"; 
	$data=[];
	Mail::send('email.credentials', $data, function($message)
	{
		$message->to('fproperzi@gmail.com', 'Jon Doe')->subject('Welcome!');
	});
});
$router->get('/config', function () use ($router) {



    return '<pre>'.print_r(config(),true).'</pre>';
});
$router->group(['prefix' => 'api'], function () use ($router) {
	
	$router->post('login' 				,['uses' => 'AuthController@login']);
	$router->post('logout' 				,['uses' => 'AuthController@logout']);
	$router->post('refresh' 			,['uses' => 'AuthController@refresh']);
	$router->post('recoverpwd' 			,['uses' => 'AuthController@recoverpwd']);
	});


	
	$router->get('users'  					,['uses' => 'UserController@showAll']);
	$router->get('users/{id}' 				,['uses' => 'UserController@showOne']);
	$router->post('users' 					,['uses' => 'UserController@create']);
	$router->put('users/{id}'				,['uses' => 'UserController@update']);
	$router->delete('users/{id}'			,['uses' => 'UserController@delete']);



$router->get('authors' 											,['uses' => 'AuthorController@showAll']);
$router->get('authors/{id}' 									,['uses' => 'AuthorController@showOne']);
$router->post('authors' 										,['uses' => 'AuthorController@create']);
$router->put('authors/{id}' 									,['uses' => 'AuthorController@update']);
$router->delete('authors/{id}'									,['uses' => 'AuthorController@delete']);

$router->post('authors/{author_id}/books'						,['uses' => 'AuthorController@createBook']);
$router->put('authors/{author_id}/books/{book_id}'				,['uses' => 'AuthorController@updateBook']);
$router->delete('authors/{author_id}/books/{book_id}' 			,['uses' => 'AuthorController@deleteBook']);

$router->get('books'											,['uses' => 'AuthorController@showAllBooks']);
$router->get('authors/{author_id}/books'						,['uses' => 'AuthorController@showAllBooksFromAuthor']);
$router->get('authors/{author_id}/books/{book_id}'				,['uses' => 'AuthorController@showOneBook']);



$router->get('impianti' 										,['uses' => 'ImpiantoController@showAll']);
$router->get('impianti/tipi' 									,['uses' => 'ImpiantoController@NuImpiantixTipo']);
$router->get('impianti/tipi/{tipo_id}' 							,['uses' => 'ImpiantoController@ImpiantixTipo']);
$router->get('impianti/comuni'					 				,['uses' => 'ImpiantoController@NuImpiantixComune']);
$router->get('impianti/comuni/{comune_id}/tipi' 				,['uses' => 'ImpiantoController@TipiImpiantoxComune']);
$router->get('impianti/comuni/{comune_id}/tipi/{tipo_id}' 		,['uses' => 'ImpiantoController@ImpiantixTipoxComune']);
$router->get('impianti/{impianto_id}/interventi' 				,['uses' => 'ImpiantoController@InterventixImpianto']);
$router->get('impianti/interventi/{intervento_id}' 				,['uses' => 'ImpiantoController@Intervento']);

	





