<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Account extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_accounts';
	public $incrementing = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['id', 'name', 'nickName', 'type', 'avatar', 'mobile', 'email',
		'srcId', 'srcUrl', 'token', 'expire_time', 'info'];
}
