<?php

namespace Gmf\Sys;

use DateInterval;
use Gmf\Sys\Bridge;
use Gmf\Sys\Bridge\AuthCodeRepository;
use Gmf\Sys\Bridge\PersonalAccessGrant;
use Gmf\Sys\Bridge\RefreshTokenRepository;
use Gmf\Sys\Bridge\UserRepository;
use Gmf\Sys\Guards\TokenGuard;
use Gmf\Sys\Passport\ClientRepository as PassportClientRepository;
use Gmf\Sys\Passport\Passport;
use Gmf\Sys\Passport\TokenRepository as PassportTokenRepository;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\RequestGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;

class ServiceProvider extends BaseServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot() {
		if ($this->app->runningInConsole()) {
			$this->commands([
				Console\Publish\PublishlCommand::class,
				Console\Install\InstallCommand::class,
				Console\Install\SeedCommand::class,
				Console\Install\SqlCommand::class,
				Console\Install\MdCommand::class,

				Console\Passport\InstallCommand::class,
				Console\Passport\ClientCommand::class,
				Console\Passport\KeysCommand::class,

				Console\Create\MdCommand::class,
				Console\Create\PostSeedCommand::class,
				Console\Create\PreSeedCommand::class,
				Console\Create\SeedCommand::class,
				Console\Create\ControllerCommand::class,
				Console\Create\ModelCommand::class,
				Console\Create\PageCommand::class,
			]);
		} else {
			$this->commands([
				Console\Install\SeedCommand::class,
				Console\Install\SqlCommand::class,
			]);
		}
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'gmf');
		$this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
		$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

		$this->deleteCookieOnLogout();

		if ($this->app->runningInConsole()) {
			$publishes = config('gmf.publishes', 'gmf');

			$this->publishes([
				__DIR__ . '/../config/gmf.php' => config_path('gmf.php'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/assets/js' => resource_path('assets/js/vendor/gmf-sys'),
				__DIR__ . '/../resources/public' => public_path('assets/vendor/gmf-sys'),
			], $publishes);
		}
	}
	public function alias() {
		return 'gmf-sys';
	}
	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->registerAuthorizationServer();

		$this->registerResourceServer();

		$this->registerGuard();

		$this->registerUuid();

		$this->registerEnt();

		$this->registerPackager();
	}
	protected function registerUuid() {
		$this->app->singleton('uuid', function ($app) {
			return new Uuid\Manager($app);
		});
	}
	protected function registerEnt() {
		$this->app->singleton('gauth', function ($app) {
			return new GAuth\GAuthManager($app);
		});
	}
	protected function registerPackager() {
		$this->app->singleton('packager', function ($app) {
			return tap(new Packager\PackagerManager($app), function ($grant) {
				$grant->loadDatabasesFrom(__DIR__ . '/../database/');
			});
		});
	}
	public function provides() {
		return [
			'gauth', 'packager',
		];
	}

	/**
	 * Register the authorization server.
	 *
	 * @return void
	 */
	protected function registerAuthorizationServer() {
		$this->app->singleton(AuthorizationServer::class, function () {
			return tap($this->makeAuthorizationServer(), function ($server) {
				$server->enableGrantType(
					$this->makeAuthCodeGrant(), Passport::tokensExpireIn()
				);

				$server->enableGrantType(
					$this->makeRefreshTokenGrant(), Passport::tokensExpireIn()
				);

				$server->enableGrantType(
					$this->makePasswordGrant(), Passport::tokensExpireIn()
				);

				$server->enableGrantType(
					new PersonalAccessGrant, new DateInterval('P1Y')
				);

				$server->enableGrantType(
					new ClientCredentialsGrant, Passport::tokensExpireIn()
				);

				if (Passport::$implicitGrantEnabled) {
					$server->enableGrantType(
						$this->makeImplicitGrant(), Passport::tokensExpireIn()
					);
				}
			});
		});
	}

	/**
	 * Create and configure an instance of the Auth Code grant.
	 *
	 * @return \League\OAuth2\Server\Grant\AuthCodeGrant
	 */
	protected function makeAuthCodeGrant() {
		return tap($this->buildAuthCodeGrant(), function ($grant) {
			$grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());
		});
	}

	/**
	 * Build the Auth Code grant instance.
	 *
	 * @return \League\OAuth2\Server\Grant\AuthCodeGrant
	 */
	protected function buildAuthCodeGrant() {
		return new AuthCodeGrant(
			$this->app->make(AuthCodeRepository::class),
			$this->app->make(RefreshTokenRepository::class),
			new DateInterval('PT10M')
		);
	}

	/**
	 * Create and configure a Refresh Token grant instance.
	 *
	 * @return \League\OAuth2\Server\Grant\RefreshTokenGrant
	 */
	protected function makeRefreshTokenGrant() {
		$repository = $this->app->make(RefreshTokenRepository::class);

		return tap(new RefreshTokenGrant($repository), function ($grant) {
			$grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());
		});
	}

	/**
	 * Create and configure a Password grant instance.
	 *
	 * @return \League\OAuth2\Server\Grant\PasswordGrant
	 */
	protected function makePasswordGrant() {
		$grant = new PasswordGrant(
			$this->app->make(UserRepository::class),
			$this->app->make(RefreshTokenRepository::class)
		);

		$grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

		return $grant;
	}

	/**
	 * Create and configure an instance of the Implicit grant.
	 *
	 * @return \League\OAuth2\Server\Grant\ImplicitGrant
	 */
	protected function makeImplicitGrant() {
		return new ImplicitGrant(Passport::tokensExpireIn());
	}

	/**
	 * Make the authorization service instance.
	 *
	 * @return \League\OAuth2\Server\AuthorizationServer
	 */
	public function makeAuthorizationServer() {
		return new AuthorizationServer(
			$this->app->make(Bridge\ClientRepository::class),
			$this->app->make(Bridge\AccessTokenRepository::class),
			$this->app->make(Bridge\ScopeRepository::class),
			$this->makeCryptKey('oauth-private.key'),
			app('encrypter')->getKey()
		);
	}

	/**
	 * Register the resource server.
	 *
	 * @return void
	 */
	protected function registerResourceServer() {
		$this->app->singleton(ResourceServer::class, function () {
			return new ResourceServer(
				$this->app->make(Bridge\AccessTokenRepository::class),
				$this->makeCryptKey('oauth-public.key')
			);
		});
	}

	/**
	 * Create a CryptKey instance without permissions check
	 *
	 * @param string $key
	 * @return \League\OAuth2\Server\CryptKey
	 */
	protected function makeCryptKey($key) {
		return new CryptKey(
			'file://' . Passport::keyPath($key),
			null,
			false
		);
	}

	/**
	 * Register the token guard.
	 *
	 * @return void
	 */
	protected function registerGuard() {
		Auth::extend('passport', function ($app, $name, array $config) {
			return tap($this->makeGuard($config), function ($guard) {
				$this->app->refresh('request', $guard, 'setRequest');
			});
		});
	}

	/**
	 * Make an instance of the token guard.
	 *
	 * @param  array  $config
	 * @return \Illuminate\Auth\RequestGuard
	 */
	protected function makeGuard(array $config) {
		return new RequestGuard(function ($request) use ($config) {
			return (new TokenGuard(
				$this->app->make(ResourceServer::class),
				Auth::createUserProvider($config['provider']),
				$this->app->make(PassportTokenRepository::class),
				$this->app->make(PassportClientRepository::class),
				$this->app->make('encrypter')
			))->user($request);
		}, $this->app['request']);
	}

	/**
	 * Register the cookie deletion event handler.
	 *
	 * @return void
	 */
	protected function deleteCookieOnLogout() {
		Event::listen(Logout::class, function () {
			if (Request::hasCookie(Passport::cookie())) {
				Cookie::queue(Cookie::forget(Passport::cookie()));
			}
		});
	}
}