<?php

namespace Gmf\Sys\Http\Middleware;

use Closure;
use Gmf\Sys\Exceptions\MissingScopeException;
use Illuminate\Auth\AuthenticationException;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

class CheckClientCredentials {
	/**
	 * The Resource Server instance.
	 *
	 * @var \League\OAuth2\Server\ResourceServer
	 */
	private $server;

	/**
	 * Create a new middleware instance.
	 *
	 * @param  \League\OAuth2\Server\ResourceServer  $server
	 * @return void
	 */
	public function __construct(ResourceServer $server) {
		$this->server = $server;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  mixed  ...$scopes
	 * @return mixed
	 * @throws \Illuminate\Auth\AuthenticationException
	 */
	public function handle($request, Closure $next, ...$scopes) {
		$psr = (new DiactorosFactory)->createRequest($request);

		try {
			$psr = $this->server->validateAuthenticatedRequest($psr);
			$request->oauth_client_id = $psr->getAttribute('oauth_client_id');
			$request->oauth_access_token_id = $psr->getAttribute('oauth_access_token_id');
		} catch (OAuthServerException $e) {
			throw new AuthenticationException;
		}

		$this->validateScopes($psr, $scopes);

		return $next($request);
	}

	/**
	 * Validate the scopes on the incoming request.
	 *
	 * @param  \Psr\Http\Message\ResponseInterface $psr
	 * @param  array  $scopes
	 * @return void
	 * @throws \Gmf\Passport\Exceptions\MissingScopeException
	 */
	protected function validateScopes($psr, $scopes) {
		if (in_array('*', $tokenScopes = $psr->getAttribute('oauth_scopes'))) {
			return;
		}

		foreach ($scopes as $scope) {
			if (!in_array($scope, $tokenScopes)) {
				throw new MissingScopeException($scope);
			}
		}
	}
}
