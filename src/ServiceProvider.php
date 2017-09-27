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
				__DIR__ . '/../resources/git/database.gitignore' => database_path('.gitignore'),
				__DIR__ . '/../resources/git/allfiles.gitignore' => resource_path('assets/js/vendor/.gitignore'),
				__DIR__ . '/../resources/git/allfiles.gitignore' => resource_path('assets/fonts/vendor/.gitignore'),
				__DIR__ . '/../resources/git/allfiles.gitignore' => resource_path('assets/sass/vendor/.gitignore'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/assets/fonts' => resource_path('assets/fonts/vendor/gmf-sys'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/assets/js' => resource_path('assets/js/vendor/gmf-sys'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/assets/sass' => resource_path('assets/sass/vendor/gmf-sys'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/assets/img' => public_path('img/vendor/gmf-sys'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../database/seeds' => database_path('seeds'),
				__DIR__ . '/../database/preseeds' => database_path('preseeds'),
				__DIR__ . '/../database/postseeds' => database_path('postseeds'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../database/sqls' => database_path('sqls'),
				__DIR__ . '/../database/presqls' => database_path('presqls'),
				__DIR__ . '/../database/postsqls' => database_path('postsqls'),
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
