<?php

namespace Gmf\Sys\Models\Authority;
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
}
