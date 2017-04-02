<?php

namespace Gmf\Sys\Libs;
use Closure;
use Gmf\Sys\Builder;

class APIResult {
	public static function pager($result, Closure $callback = null) {
		$data = [];
		$pager = [];
		if (isset($result['data'])) {
			$data = $result['data'];
			//total--总记录数
			$pager['total'] = $result['total'];
			//per_page --每页记录数
			$pager['per_page'] = $result['per_page'];
			//current_page--当前页数
			$pager['current_page'] = $result['current_page'];
			//last_page--尾页页码
			$pager['last_page'] = $result['last_page'];

		} else if (isset($result->data)) {
			$data = $result->data;
			//total--总记录数
			$pager['total'] = $result->total;
			//per_page --每页记录数
			$pager['per_page'] = $result->per_page;
			//current_page--当前页数
			$pager['current_page'] = $result->current_page;
			//last_page--尾页页码
			$pager['last_page'] = $result->last_page;
		}
		$builder = new Builder(compact('data', 'pager'));
		return static::toResult($builder, $callback);
	}
	public static function json($data, Closure $callback = null) {
		$builder = new Builder(compact('data'));
		return static::toResult($builder, $callback);
	}
	public static function errorParam($paramName) {
		$code = 2000;
		$msg = '参数[' . $paramName . ']不合法或者为空';
		$builder = new Builder(compact('msg', 'code'));
		return static::toResult($builder, $callback);
	}
	public static function error($msg, Closure $callback = null) {
		$code = 2000;
		$builder = new Builder(compact('msg', 'code'));
		return static::toResult($builder, $callback);
	}
	private static function toResult(Builder $builder, Closure $callback = null) {
		if (empty($builder->code)) {
			$builder->code = 0;
		}
		if (!is_null($callback)) {
			$callback($builder);
		}
		//code,data,msg,error,meta
		$data = $builder->toArray();
		if ($builder->code) {
			return response()->json($data, 400);
		} else {
			return response()->json($data);
		}
	}
}
