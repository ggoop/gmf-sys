<?php
namespace Gmf\Sys\Http\Middleware;

use Auth;
use Carbon\Carbon;
use Closure;
use Gmf\Sys\Models\Visitor;

class VisitorMiddleware {

	public function handle($request, Closure $next) {
		$headers = $request->headers;
		$server = $request->server;
		$query = $request->query();
		$params = $request->all();
		$inData = [];
		$inData['ip'] = $request->ip();
		$inData['path'] = $request->path();
		$inData['url'] = $request->url();
		$inData['method'] = $request->method();

		$inData['user_id'] = Auth::id();

		if ($request->hasHeader('Ent')) {
			$inData['ent_id'] = $request->header('Ent');
		}

		$inData['query'] = json_encode($query);
		$inData['header'] = json_encode($headers->all());
		$inData['body'] = $request->getContent();
		$inData['content_type'] = $request->getContentType();

		$inData['input'] = json_encode($request->input());

		$inData['agent'] = $request->userAgent();

		if ($server) {
			$inData['referer'] = $server->get('HTTP_REFERER');
		}
		$inData['created_at'] = Carbon::now();

		if (!empty($params['client_name'])) {
			$inData['client_name'] = $params['client_name'];
		}
		if (!empty($params['client_sn'])) {
			$inData['client_sn'] = $params['client_sn'];
		}
		if (!empty($params['client_account'])) {
			$inData['client_account'] = $params['client_account'];
		}
		if (!empty($params['client_id'])) {
			$inData['client_id'] = $params['client_id'];
		}

		$fromTime = microtime(true);

		$response = $next($request);

		$endTime = microtime(true);
		$inData['times'] = ($endTime - $server->get('REQUEST_TIME_FLOAT')) * 1000;
		$inData['actimes'] = ($endTime - $fromTime) * 1000;
		Visitor::create($inData);
		return $response;
	}
}
