<?php

namespace Gmf\Sys\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class DtiLocal extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_dti_locals';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'host', 'path', 'method_enum', 'header', 'body'];

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'code', 'name', 'host', 'path', 'method_enum', 'header', 'body']);

			$find = ['code' => $data['code']];
			if (!empty($data['ent_id'])) {
				$find['ent_id'] = $data['ent_id'];
			}

			static::updateOrCreate($find, $data);
		});
	}
}
