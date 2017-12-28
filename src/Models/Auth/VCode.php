<?php

namespace Gmf\Sys\Models\Auth;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class VCode extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_auth_vcodes';
	public $incrementing = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['id', 'user_id', 'type', 'token', 'expire_time'];
}
