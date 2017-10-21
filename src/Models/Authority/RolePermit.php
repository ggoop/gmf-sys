<?php

namespace Gmf\Sys\Models\Authority;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class RolePermit extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_authority_role_permits';
	public $incrementing = false;
	protected $fillable = ['ent_id', 'role_id', 'permit_id', 'opinion_enum'];

	public function role() {
		return $this->belongsTo('Gmf\Sys\Models\Authority\Role');
	}
	public function permit() {
		return $this->belongsTo('Gmf\Sys\Models\Authority\Permit');
	}
	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			$data = array_only($builder->toArray(), ['id', 'ent_id', 'role_id', 'permit_id', 'opinion_enum']);

			if (!empty($builder->role)) {
				$tmp = Role::where('code', $builder->role)->where('ent_id', $builder->ent_id)->first();
				$data['role_id'] = $tmp ? $tmp->id : '';
			}
			if (!empty($builder->permit)) {
				$tmp = Permit::where('code', $builder->permit)->first();
				$data['permit_id'] = $tmp ? $tmp->id : '';
			}
			static::updateOrCreate(['ent_id' => $data['ent_id'], 'role_id' => $data['role_id'], 'permit_id' => $data['permit_id']], $data);
		});
	}
}
