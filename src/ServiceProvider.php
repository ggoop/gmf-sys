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
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'gmf');
		if ($this->app->runningInConsole()) {
			$this->registerMigrations();

			$publishes = config('gmf.publishes', 'gmf');

			$this->publishes([
				__DIR__ . '/../config/gmf.php' => config_path('gmf.php'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/assets/fonts' => base_path('resources/assets/fonts/vendor/gmf-sys'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/assets/js' => base_path('resources/assets/js/vendor/gmf-sys'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/assets/sass' => base_path('resources/assets/sass/vendor/gmf-sys'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/assets/img' => base_path('public/img'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/assets/css' => base_path('public/css'),
			], $publishes);

			$this->commands([
				Console\InstallCommand::class,
				Console\ClientCommand::class,
				Console\KeysCommand::class,
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
	}
}
