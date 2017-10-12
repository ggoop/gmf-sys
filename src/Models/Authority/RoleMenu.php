<?php

namespace Gmf\Sys\Models\Authority;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_authority_role_menus';
	public $incrementing = false;
	protected $fillable = ['role_id', 'menu_id'];
}
