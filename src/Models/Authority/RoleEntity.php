<?php

namespace Gmf\Sys\Models\Authority;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class RoleEntity extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_authority_role_entities';
	public $incrementing = false;
	protected $fillable = ['ent_id', 'role_id', 'entity_id', 'filter', 'operation_enum'];

	public function role() {
		return $this->belongsTo('Gmf\Sys\Models\Authority\Role');
	}
	public function entity() {
		return $this->belongsTo('Gmf\Sys\Models\Entity');
	}
}
