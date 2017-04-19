<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
	use HasApiTokens, Notifiable;

	protected $table = 'gmf_sys_users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password', 'type', 'avatar', 'mobile', 'status_enum',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];
	public static function registerByAccount($type, Array $opts = []) {
		$user = false;
		$opts['type'] = $type;
		$query = Account::where('type', $type);
		if (isset($opts['mobile'])) {
			$query->where('mobile', $opts['mobile']);
		}
		if (isset($opts['email'])) {
			$query->where('email', $opts['email']);
		}
		if (isset($opts['srcId'])) {
			$query->where('srcId', $opts['srcId']);
		}
		$acc = $query->first();
		if (!$acc) {
			$acc = Account::create($opts);
		}
		$userAcc = UserAccount::where('account_id', $acc->id)->first();
		if (!$userAcc) {
			$data = array_only($opts, ['name', 'email', 'mobile', 'type', 'avatar', 'password']);
			if (!empty($data['password'])) {
				$data['password'] = bcrypt($data['password']);
			}
			$user = User::create($data);
		} else {
			$user = User::find($userAcc->user_id);
		}
		return $user;
	}
}
