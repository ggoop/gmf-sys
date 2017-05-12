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
		$this->forOrg();
	}

	public function forSys() {
		$this->router->group(['prefix' => 'sys', 'middleware' => ['api']], function ($router) {
			$router->resource('datas', 'DataController', ['only' => ['index', 'show']]);
		});
		$this->router->group(['prefix' => 'sys', 'middleware' => ['api', 'auth:api']], function ($router) {

			$router->get('/enums/{enum}', ['uses' => 'EntityController@getEnum']);
			$router->resource('entities', 'EntityController', ['only' => ['index', 'show']]);

			$router->get('/queries/query/{query}', ['uses' => 'QueryController@query']);
			$router->resource('queries', 'QueryController', ['only' => ['index', 'show']]);

			$router->get('/menus/all', ['uses' => 'MenuController@all']);
			$router->resource('menus', 'MenuController', ['only' => ['index', 'show']]);

			$router->resource('languages', 'LanguageController', ['only' => ['index', 'show']]);

			$router->post('/profiles/batch', ['uses' => 'ProfileController@batchStore']);
			$router->resource('profiles', 'ProfileController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
		});
	}
	public function forOrg() {
		//auth:api
		//client_credentials
		$this->router->group(['prefix' => 'org', 'middleware' => ['api', 'auth:api']], function ($router) {

			$router->resource('orgs', 'OrgOrgController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('depts', 'OrgDeptController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('works', 'OrgWorkController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('teams', 'OrgTeamController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
		});
	}
}
