<?php

namespace Gmf\Sys\Models\Authority;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_authority_role_menus';
	public $incrementing = false;
	protected $fillable = ['ent_id', 'role_id', 'menu_id', 'opinion_enum'];
	public function role() {
		return $this->belongsTo('Gmf\Sys\Models\Authority\Role');
	}
	public function menu() {
		return $this->belongsTo('Gmf\Sys\Models\Menu');
	}
}
