<?php

namespace Gmf\Sys\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class DtiParam extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_dti_params';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'category_id', 'dti_id', 'code', 'name', 'type_enum', 'value', 'is_revoked'];
	protected $casts = [
		'is_revoked' => 'boolean',
	];
	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'category_id', 'dti_id', 'code', 'name', 'type_enum', 'value', 'is_revoked']);
			$ent_id = '';
			if (!empty($builder->ent_id)) {
				$ent_id = $builder->ent_id;
			}
			if (!empty($builder->category)) {
				$t = DtiCategory::where('code', $builder->category)->where('ent_id', $ent_id)->first();
				if ($t) {
					$data['category_id'] = $t->id;
				}
			}
			if (!empty($builder->dti)) {
				$t = Dti::where('code', $builder->dti)->where('ent_id', $ent_id)->first();
				if ($t) {
					$data['dti_id'] = $t->id;
				}
			}
			static::create($data);
		});
	}
}
