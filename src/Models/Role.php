<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Role extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_roles';
	public $incrementing = false;
	protected $fillable = ['id', 'code', 'name', 'memo'];
}
