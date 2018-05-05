<?php

namespace Gmf\Sys\Models\Authority;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Models\Entity;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class RoleEntity extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_authority_role_entities';

	protected $fillable = ['ent_id', 'role_id', 'entity_id', 'filter', 'operation_enum'];

	public function role() {
		return $this->belongsTo('Gmf\Sys\Models\Authority\Role');
	}
	public function entity() {
		return $this->belongsTo('Gmf\Sys\Models\Entity');
	}
	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			$data = array_only($builder->toArray(), ['id', 'ent_id', 'role_id', 'entity_id']);

			if (!empty($builder->role)) {
				$tmp = Role::where('code', $builder->role)->where('ent_id', $builder->ent_id)->first();
				$data['role_id'] = $tmp ? $tmp->id : '';
			}
			if (!empty($builder->entity)) {
				$tmp = Entity::where('name', $builder->entity)->first();
				$data['entity_id'] = $tmp ? $tmp->id : '';
			}
			static::updateOrCreate(['ent_id' => $data['ent_id'], 'role_id' => $data['role_id'], 'entity_id' => $data['entity_id']], $data);
		});
	}
}
