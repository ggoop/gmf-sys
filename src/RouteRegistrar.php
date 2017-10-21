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
			$router->post('datas', ['uses' => 'DataController@index']);
			$router->any('datas/test', ['uses' => 'DataController@test']);
			$router->resource('datas', 'DataController', ['only' => ['index', 'show']]);
			$router->resource('components', 'ComponentController', ['only' => ['index', 'show']]);
		});
		$this->router->group(['prefix' => 'sys', 'middleware' => ['api', 'auth:api', 'ent_check']], function ($router) {
			$router->get('/entities/pager', ['uses' => 'EntityController@pager']);
			$router->get('/enums/all', ['uses' => 'EntityController@getAllEnums']);
			$router->get('/enums/{enum}', ['uses' => 'EntityController@getEnum']);
			$router->resource('entities', 'EntityController', ['only' => ['index', 'show']]);

			$router->post('/queries/query/{query}', ['uses' => 'QueryController@query']);
			$router->resource('queries', 'QueryController', ['only' => ['index', 'show']]);

			$router->get('/menus/all', ['uses' => 'MenuController@all']);
			$router->get('/menus/path/{id}', ['uses' => 'MenuController@getPath']);
			$router->resource('menus', 'MenuController', ['only' => ['index', 'show']]);

			$router->resource('languages', 'LanguageController', ['only' => ['index', 'show']]);

			$router->resource('users', 'UserController', ['only' => ['index', 'show']]);

			$router->post('/profiles/batch', ['uses' => 'ProfileController@batchStore']);
			$router->resource('profiles', 'ProfileController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->get('/ents/my', ['uses' => 'EntController@getMyEnts']);
			$router->any('/ents/seed/{id}', ['uses' => 'EntController@seedDatas']);
			$router->resource('ents', 'EntController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->resource('dtis', 'DtiController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('dti-categories', 'DtiCategoryController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
			$router->resource('dti-params', 'DtiParamController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

		});

		$this->router->group(['prefix' => 'sys/authority', 'middleware' => ['api', 'auth:api', 'ent_check']], function ($router) {

			$router->post('/roles/batch', ['uses' => 'Authority\RoleController@batchStore']);
			$router->resource('roles', 'Authority\RoleController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->post('/permits/batch', ['uses' => 'Authority\PermitController@batchStore']);
			$router->resource('permits', 'Authority\PermitController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);

			$router->resource('role-entities', 'Authority\RoleEntityController', ['only' => ['index', 'store', 'destroy']]);

			$router->resource('role-permits', 'Authority\RolePermitController', ['only' => ['index', 'store', 'destroy']]);

			$router->resource('role-menus', 'Authority\RoleMenuController', ['only' => ['index', 'store', 'destroy']]);

			$router->resource('role-users', 'Authority\RoleUserController', ['only' => ['index', 'store', 'destroy']]);

		});

	}
}
