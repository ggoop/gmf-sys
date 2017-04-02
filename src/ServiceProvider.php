<?php

namespace Gmf\Sys;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot() {
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'sys');
		if ($this->app->runningInConsole()) {
			$this->registerMigrations();

			$this->publishes([
				__DIR__ . '/../resources/assets/fonts' => base_path('resources/assets/fonts/vendor/sys'),
			], 'gmf');

			$this->publishes([
				__DIR__ . '/../resources/views' => base_path('resources/views/vendor/sys'),
			], 'gmf');

			$this->publishes([
				__DIR__ . '/../resources/assets/js' => base_path('resources/assets/js/vendor/sys'),
			], 'gmf');

			$this->publishes([
				__DIR__ . '/../resources/assets/sass' => base_path('resources/assets/sass/vendor/sys'),
			], 'gmf');

			$this->publishes([
				__DIR__ . '/../resources/assets/img' => base_path('public/img'),
			], 'gmf');

			$this->commands([
				Console\InstallCommand::class,
				Console\ClientCommand::class,
			]);
		}
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {

	}
	/**
	 * Register Passport's migration files.
	 *
	 * @return void
	 */
	protected function registerMigrations() {
		if (Sys::$runsMigrations) {
			return $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
		}
		$this->publishes([
			__DIR__ . '/../database/migrations' => database_path('migrations'),
		], 'gmf-migrations');
	}
}
