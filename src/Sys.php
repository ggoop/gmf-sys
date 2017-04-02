<?php

namespace Gmf\Sys;
use Illuminate\Support\Facades\Route;

class Sys {
	public static $runsMigrations = true;
	/**
	 * Get a Passport route registrar.
	 *
	 * @param  array  $options
	 * @return RouteRegistrar
	 */
	public static function apiRoutes($callback = null, array $options = []) {
		$callback = $callback ?: function ($router) {
			$router->all();
		};

		$defaultOptions = [
			'prefix' => 'api',
			'middleware' => 'api',
			'namespace' => '\Gmf\Sys\Http\Controllers',
		];

		$options = array_merge($defaultOptions, $options);

		Route::group($options, function ($router) use ($callback) {
			$callback(new RouteRegistrar($router));
		});
	}
	/**
	 * Get a Passport route registrar.
	 *
	 * @param  array  $options
	 * @return RouteRegistrar
	 */
	public static function routes() {
		static::apiRoutes();
	}
	/**
	 * Configure Passport to not register its migrations.
	 *
	 * @return static
	 */
	public static function ignoreMigrations() {
		static::$runsMigrations = false;
		return new static;
	}
}