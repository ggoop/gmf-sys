<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_permits';
	public $incrementing = false;
	protected $fillable = ['id', 'code', 'name', 'memo'];
}
