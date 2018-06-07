<?php
namespace Gmf\Sys\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Exception;
use GAuth;
use Gmf\Sys\Models\Visitor;
use Log;

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

		
		
		if (!empty($params['user_name'])) {
			$inData['user_name'] = str_limit($params['user_name'], 250);
		}
		if ($request->hasHeader('Ent')) {
			$inData['ent_id'] = $request->header('Ent');
		}

		$inData['query'] = json_encode($query);
		$inData['header'] = json_encode($headers->all());
		$inData['body'] = $request->getContent();
		$inData['content_type'] = $request->getContentType();
		if (!empty($params['trace'])&&$v=$params['trace']) {
			$inData['trace'] = $v;
		}
		$inData['input'] = json_encode($request->input());

		$inData['agent'] = $request->userAgent();

		if ($server) {
			$inData['referer'] = str_limit($server->get('HTTP_REFERER'), 250);
		}
		$inData['created_at'] = Carbon::now();

		if (!empty($params['app_id'])) {
			$inData['app_id'] = str_limit($params['app_id'], 250);
		}
		if (!empty($params['app_name'])) {
			$inData['app_name'] = str_limit($params['app_name'], 250);
		}		

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
		try {			
			$response = $next($request);
			return $response;
		} catch (\Exception $e) {
			throw $e;
		} finally {
			$endTime = microtime(true);
			$inData['times'] = ($endTime - $server->get('REQUEST_TIME_FLOAT')) * 1000;
			$inData['actimes'] = ($endTime - $fromTime) * 1000;
			try {
				$inData['ent_id'] = GAuth::entId();
				$inData['user_id'] = GAuth::id();
				Visitor::create($inData);
			} catch (\Exception $e) {
				Log::error('Visitor create error:');
			}
		}

	}
}
