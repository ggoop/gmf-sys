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
		static::authRoutes();
	}
	public static function authRoutes() {
		Route::group(['middleware' => ['web']], function () {
			Route::get('login', '\Gmf\Sys\Http\Controllers\AuthController@getLogin')->name('login');
			Route::post('login', '\Gmf\Sys\Http\Controllers\AuthController@postLogin');
			Route::get('logout', '\Gmf\Sys\Http\Controllers\AuthController@getLogout')->name('logout');

			// Registration Routes...
			Route::get('register', '\Gmf\Sys\Http\Controllers\AuthController@getRegister')->name('register');
			Route::post('register', '\Gmf\Sys\Http\Controllers\AuthController@postRegister');

			// Password Reset Routes...
			Route::get('password/email', '\Gmf\Sys\Http\Controllers\AuthController@getEmail')->name('password.email');
			Route::post('password/email', '\Gmf\Sys\Http\Controllers\AuthController@postEmail');
			Route::get('password/reset/{token}', '\Gmf\Sys\Http\Controllers\AuthController@getReset')->name('password.reset');
			Route::post('password/reset', '\Gmf\Sys\Http\Controllers\AuthController@postReset');
		});

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