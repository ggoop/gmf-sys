<?php
namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_user_accounts';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['user_id', 'account_id', 'is_default'];
}
