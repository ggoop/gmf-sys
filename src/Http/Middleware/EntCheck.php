<?php
namespace Gmf\Sys\Http\Middleware;

use Closure;

class EntCheck {

	public function handle($request, Closure $next) {
		if ($request->hasHeader('Ent')) {
			$request->oauth_ent_id = $request->header('Ent');
		}
		return $next($request);
	}
}
