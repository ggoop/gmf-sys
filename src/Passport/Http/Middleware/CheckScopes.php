<?php

namespace Gmf\Sys\Passport\Http\Middleware;

use Gmf\Sys\Exceptions\MissingScopeException;
use Illuminate\Auth\AuthenticationException;

class CheckScopes {
	/**
	 * Handle the incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  array  $scopes
	 * @return \Illuminate\Http\Response
	 */
	public function handle($request, $next, ...$scopes) {
		if (!$request->user() || !$request->user()->token()) {
			throw new AuthenticationException;
		}

		foreach ($scopes as $scope) {
			if (!$request->user()->tokenCan($scope)) {
				throw new MissingScopeException($scope);
			}
		}

		return $next($request);
	}
}
