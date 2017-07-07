<?php

namespace Gmf\Sys;

use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar {
	/**
	 * The router implementation.
	 *
	 * @var Router
	 */
	protected $router;

	/**
	 * Create a new route registrar instance.
	 *
	 * @param  Router  $router
	 * @return void
	 */
	public function __construct(Router $router) {
		$this->router = $router;
	}

	/**
	 * Register routes for transient tokens, clients, and personal access tokens.
	 *
	 * @return void
	 */
	public function all() {

		$this->forSys();
	}

	public function forSys() {
		$this->router->group(['prefix' => 'sys', 'middleware' => ['api']], function ($router) {
			$router->resource('datas', 'DataController', ['only' => ['index', 'show']]);
		});
		$this->router->group(['prefix' => 'sys', 'middleware' => ['api', 'auth:api', 'ent_check']], function ($router) {

			$router->get('/enums/{enum}', ['uses' => 'EntityController@getEnum']);
			$router->resource('entities', 'EntityController', ['only' => ['index', 'show']]);

			$router->post('/queries/query/{query}', ['uses' => 'QueryController@query']);
			$router->resource('queries', 'QueryController', ['only' => ['index', 'show']]);

			$router->get('/menus/all', ['uses' => 'MenuController@all']);
			$router->get('/menus/path/{id}', ['uses' => 'MenuController@getPath']);
			$router->resource('menus', 'MenuController', ['only' => ['index', 'show']]);

			$router->resource('languages', 'LanguageController', ['only' => ['index', 'show']]);

			$router->post('/profiles/batch', ['uses' => 'ProfileController@batchStore']);
			$router->resource('profiles', 'ProfileController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->get('/ents/my', ['uses' => 'EntController@getMyEnts']);

			$router->resource('dtis', 'DtiController', ['only' => ['index', 'show']]);
		});
	}
}
