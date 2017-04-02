<?php

namespace Gmf\Sys\Http\Controllers;
use Closure;
use Gmf\Sys\Libs\APIResult;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	protected function toPager($result, Closure $callback = null) {
		return APIResult::pager($result, $callback);
	}
	protected function toJson($result, Closure $callback = null) {
		return APIResult::json($result, $callback);
	}
	protected function errorParam($paramName, Closure $callback = null) {
		return APIResult::errorParam($paramName);
	}
	protected function toError($msg, Closure $callback = null) {
		return APIResult::error($msg, $callback);
	}
}
