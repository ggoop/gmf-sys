<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Passport\HasApiTokens;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
	use Snapshotable, HasGuard, HasApiTokens, Notifiable;

	protected $table = 'gmf_sys_users';
	public $incrementing = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['id', 'account', 'password', 'secret', 'name', 'nickName', 'email', 'type', 'avatar', 'mobile', 'status_enum'];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token', 'secret',
	];

	public function findForPassport($username) {
		return User::where('account', $username)->first();
	}
	public function validateForPassportPasswordGrant($password) {
		//var_dump($password);
		return true;
	}
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

		$acc = $query->first();
		if (!$acc) {
			$acc = Account::create($opts);
		}
		$userAcc = UserAccount::where('account_id', $acc->id)->first();
		if (!$userAcc) {
			if (!empty($opts['srcId'])) {
				$user = User::where('type', $type)->where('id', $opts['srcId'])->first();
			}
			if (!$user) {
				$data = array_only($opts, ['name', 'nickName', 'email', 'mobile', 'type', 'avatar']);
				if (!empty($data['password'])) {
					$data['secret'] = base64_encode($data['password']);
					$data['password'] = bcrypt($data['password']);
				}
				$user = User::create($data);
			}
			UserAccount::create(['user_id' => $user->id, 'account_id' => $acc->id]);
		} else {
			$user = User::find($userAcc->user_id);
		}
		return $user;
	}
}
