<?php

namespace Gmf\Sys\Models\Authority;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_authority_permits';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'memo'];
}
