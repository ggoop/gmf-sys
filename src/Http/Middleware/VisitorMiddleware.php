<?php
namespace Gmf\Sys\Http\Middleware;

use Auth;
use Carbon\Carbon;
use Closure;
use Exception;
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
			$inData['referer'] = str_limit($server->get('HTTP_REFERER'), 250);
		}
		$inData['created_at'] = Carbon::now();

		if (!empty($params['client_name'])) {
			$inData['client_name'] = str_limit($params['client_name'], 250);
		}
		if (!empty($params['client_sn'])) {
			$inData['client_sn'] = str_limit($params['client_sn'], 250);
		}
		if (!empty($params['client_account'])) {
			$inData['client_account'] = str_limit($params['client_account'], 250);
		}
		if (!empty($params['client_id'])) {
			$inData['client_id'] = $params['client_id'];
		}

		$fromTime = microtime(true);

		$response = null;
		try {
			$response = $next($request);
		} catch (\Exception $e) {
			throw $e;
		} finally {
			$endTime = microtime(true);
			$inData['times'] = ($endTime - $server->get('REQUEST_TIME_FLOAT')) * 1000;
			$inData['actimes'] = ($endTime - $fromTime) * 1000;
			Visitor::create($inData);
		}
		return $response;
	}
}
