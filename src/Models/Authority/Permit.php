<?php

namespace Gmf\Sys\Models\Authority;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_authority_permits';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'memo'];

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			$data = array_only($builder->toArray(), ['id', 'code', 'name', 'memo']);
			static::updateOrCreate(['code' => $data['code']], $data);
		});
	}
}
