<?php

namespace Gmf\Sys\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class DtiCategory extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_dti_categories';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'host', 'is_revoked'];
	protected $casts = [
		'is_revoked' => 'boolean',
	];
	public static function build(Closure $callback) {
		//id,root,parent,code,name,memo,uri,sequence
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'code', 'name', 'host', 'is_revoked']);

			static::create($data);

		});
	}
}
