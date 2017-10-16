<?php

namespace Gmf\Sys\Models\Authority;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_authority_role_users';
	public $incrementing = false;
	protected $fillable = ['ent_id', 'role_id', 'user_id'];

	public function role() {
		return $this->belongsTo('Gmf\Sys\Models\Authority\Role');
	}
	public function user() {
		return $this->belongsTo(config('gmf.user.model'));
	}
}
