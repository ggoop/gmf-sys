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
		$this->router->group(['prefix' => 'sys', 'middleware' => ['api']], function ($router) {

			$router->resource('datas', 'DataController', ['only' => ['index', 'show']]);

			$router->get('/enums/{enum}', ['uses' => 'EntityController@getEnum']);
			$router->resource('entities', 'EntityController', ['only' => ['index', 'show']]);

			$router->get('/queries/query/{query}', ['uses' => 'QueryController@queryData']);
			$router->resource('queries', 'QueryController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->get('/menus/all', ['uses' => 'MenuController@all']);
			$router->resource('menus', 'MenuController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->resource('languages', 'LanguageController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('profiles', 'ProfileController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
		});

		$this->router->group(['prefix' => 'org', 'middleware' => ['api']], function ($router) {

			$router->resource('orgs', 'OrgOrgController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('depts', 'OrgDeptController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('works', 'OrgWorkController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('teams', 'OrgTeamController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
		});
	}
}
