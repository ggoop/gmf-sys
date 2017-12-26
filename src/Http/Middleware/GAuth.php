<?php
namespace Gmf\Sys\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GAuth {

	public function handle(Request $request, Closure $next) {
		if ($request->hasHeader('Ent')) {
			$request->oauth_ent_id = $request->header('Ent');
		}
		return $next($request);
	}
}
