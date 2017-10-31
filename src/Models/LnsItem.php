<?php

namespace Gmf\Sys\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class LnsItem extends Model {
	use Snapshotable, HasGuard;
	public $timestamps = false;
	public $incrementing = false;
	protected $table = 'gmf_sys_lns_items';
	protected $fillable = ['id', 'type', 'code', 'name', 'field', 'filter'];
	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'type', 'code', 'name', 'field', 'filter']);
			$find = [];
			if (!empty($data['id'])) {
				$find['id'] = $data['id'];
			}
			if (!empty($data['code'])) {
				$find['code'] = $data['code'];
			}
			if (!empty($data['type'])) {
				$find['type'] = $data['type'];
			}
			static::updateOrCreate($find, $data);
		});
	}
}
