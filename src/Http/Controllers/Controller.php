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
	public function toJson($result, Closure $callback = null) {
		return APIResult::json($result, $callback);
	}
	public function errorParam($paramName, Closure $callback = null) {
		return APIResult::errorParam($paramName);
	}
	public function toError($msg, Closure $callback = null) {
		return APIResult::error($msg, $callback);
	}
}
