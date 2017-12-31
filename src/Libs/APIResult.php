<?php

namespace Gmf\Sys\Libs;
use Closure;
use Gmf\Sys\Builder;
use Illuminate\Container\Container;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class APIResult {
	public static function json($data, Closure $callback = null) {
		$pager = false;

		if ($data instanceof LengthAwarePaginator) {
			$pager = new Builder;
			$pager->page($data->currentPage())
				->size($data->perPage())
				->total($data->total())
				->lastPage($data->lastPage());
			$data = $data->items();
		} else if ($data instanceof Paginator) {
			$pager = new Builder;
			$pager->page($data->currentPage())
				->size($data->perPage())
				->from($data->firstItem())
				->to($data->lastItem());
			$data = $data->items();
		} else if ($data instanceof ResourceCollection) {
			$pager = new Builder;
			if ($data->resource instanceof AbstractPaginator) {
				$pager->page($data->resource->currentPage())
					->size($data->resource->perPage())
					->total($data->resource->total())
					->lastPage($data->resource->lastPage());
			}
			$res = $data->toArray(Container::getInstance()->make('request'));
			if (isset($res['data'])) {
				$data = $res['data'];
			}
		} else if ($data instanceof Resource) {
			$data = $data->toArray(Container::getInstance()->make('request'));
		}
		$builder = new Builder(compact('data'));
		if ($pager) {
			$builder->pager($pager->toArray());
		}
		return static::toResult($builder, $callback);
	}
	public static function errorParam($paramName) {
		$code = 2000;
		$msg = '参数[' . $paramName . ']不合法或者为空';
		$builder = new Builder(compact('msg', 'code'));
		return static::toResult($builder, $callback);
	}
	public static function error($msg, Closure $callback = null, $statucCode = null) {
		$code = 2000;
		$builder = new Builder(compact('msg', 'code'));
		return static::toResult($builder, $callback, $statucCode);
	}
	private static function toResult(Builder $builder, Closure $callback = null, $statucCode = null) {
		if (empty($builder->code)) {
			$builder->code = 0;
		}
		if ($builder->code) {
			$builder->errors($builder->msg);
		}
		if (!is_null($callback)) {
			$callback($builder);
		}
		//code,data,msg,error,meta
		$data = $builder->toArray();
		if ($builder->code) {
			return response()->json($data, $statucCode ?: 400);
		} else {
			return response()->json($data);
		}
	}
}
