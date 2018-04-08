<?php
namespace Gmf\Sys\Http\Middleware;
use Closure;
use Exception;
use GAuth;

class EntCheck {

	public function handle($request, Closure $next, $item = '') {
		if (!GAuth::entId()) {
			throw new Exception("没有找到企业信息!" . $item, 8000);
		}
		return $next($request);
	}
}
