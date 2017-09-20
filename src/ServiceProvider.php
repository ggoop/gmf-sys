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
			$this->commands([
				Console\PublishlCommand::class,
				Console\InstallCommand::class,
				Console\SeedCommand::class,
				Console\SqlCommand::class,
			]);

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
				__DIR__ . '/../resources/assets/img' => base_path('public/img/vendor/gmf-sys'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/git/database.gitignore' => base_path('database/.gitignore'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../database/seeds' => base_path('database/seeds'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../database/sql' => base_path('database/sql'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../database/presql' => base_path('database/presql'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../database/postsql' => base_path('database/postsql'),
			], $publishes);
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
