<?php

namespace Gmf\Sys\Models\Authority;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Role extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_authority_roles';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'memo', 'type_enum'];

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			$data = array_only($builder->toArray(), ['id', 'ent_id', 'code', 'name', 'memo']);
			static::updateOrCreate(['code' => $data['code'], 'ent_id' => $data['ent_id']], $data);
		});
	}
}
