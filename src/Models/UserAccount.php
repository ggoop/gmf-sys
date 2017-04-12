<?php
namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;

class UserAccount extends Authenticatable {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_user_accounts';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['user_id', 'account_id'];
}
