<?php
namespace Gmf\Sys\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Gmf\Sys\Models\Visitor;

class VisitorMiddleware {

	public function handle($request, Closure $next) {
		$headers = $request->headers;
		$server = $request->server;
		$inData = [];
		$inData['ip'] = $request->ip();
		$inData['path'] = $request->path();
		$inData['url'] = $request->url();
		$inData['method'] = $request->method();
		$inData['params'] = json_encode($request->input());
		if ($headers) {
			$inData['agent'] = $headers->get('user-agent');
		}
		if ($server) {
			$inData['referer'] = $server->get('HTTP_REFERER');
		}
		$inData['created_at'] = Carbon::now();
		$fromTime = microtime(true);

		$response = $next($request);

		$endTime = microtime(true);
		$inData['times'] = ($endTime - $server->get('REQUEST_TIME_FLOAT')) * 1000;
		$inData['actimes'] = ($endTime - $fromTime) * 1000;
		Visitor::create($inData);
		return $response;
	}
}
