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
	public function params() {
		return $this->hasMany('Gmf\Sys\Models\DtiParam', 'category_id');
	}
	public static function build(Closure $callback) {
		//id,root,parent,code,name,memo,uri,sequence
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'code', 'name', 'host', 'is_revoked']);

			static::updateOrCreate(['code' => $data['code'], 'ent_id' => $data['ent_id']], $data);

		});
	}
}
