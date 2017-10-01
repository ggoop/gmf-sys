<?php

namespace Gmf\Sys\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Component extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_components';
	public $incrementing = false;
	protected $fillable = ['id', 'name', 'code', 'memo', 'path'];

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			$data = array_only($builder->toArray(), ['id', 'name', 'code', 'memo', 'path']);
			static::updateOrCreate(['code' => $data['code']], $data);
		});
	}
}
