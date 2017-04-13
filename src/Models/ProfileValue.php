<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class ProfileValue extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_profile_values';
	public $incrementing = false;
	protected $fillable = ['id', 'profile_id', 'scope_id', 'scope_type', 'value'];
}
