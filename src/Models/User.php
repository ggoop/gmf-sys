<?php

namespace Gmf\Sys\Models;
use Gmf\Passport\HasApiTokens;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Validator;

class User extends Authenticatable {
	use Snapshotable, HasGuard, HasApiTokens, Notifiable;

	protected $table = 'gmf_sys_users';
	public $incrementing = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['id', 'account', 'password', 'secret', 'name', 'nick_name', 'email', 'type', 'avatar', 'mobile', 'status_enum'];

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
	public function routeNotificationForNexmo() {
		return $this->mobile;
	}
	public function validateForPassportPasswordGrant($password) {
		//var_dump($password);
		return true;
	}
	public static function registerByAccount($type, Array $opts = []) {
		$user = false;
		$opts['type'] = $type;

		if (empty($opts['account'])) {
			if (Validator::make($opts, ['email' => 'required|email'])->passes()) {
				$opts['account'] = $opts['email'];
			}
		}
		if (empty($opts['account'])) {
			if (Validator::make($opts, ['mobile' => 'required|digits:11'])->passes()) {
				$opts['account'] = $opts['mobile'];
			}
		}
		if (empty($opts['mobile']) && !empty($opts['account'])) {
			if (Validator::make($opts, ['account' => 'required|digits:11'])->passes()) {
				$opts['mobile'] = $opts['account'];
			}
		}
		if (empty($opts['email']) && !empty($opts['account'])) {
			if (Validator::make($opts, ['account' => 'required|email'])->passes()) {
				$opts['email'] = $opts['account'];
			}
		}

		if (empty($opts['name']) && !empty($opts['account'])) {
			$opts['name'] = $opts['account'];
		}
		if (empty($opts['nick_name']) && !empty($opts['name'])) {
			$opts['nick_name'] = $opts['name'];
		}
		if (empty($opts['avatar'])) {
			$opts['avatar'] = '/assets/vendor/gmf-sys/avatar/' . mt_rand(1, 50) . '.jpg';
		}

		$query = Account::where('type', $type);
		if (!empty($opts['account'])) {
			$query->where(function ($query) use ($opts) {
				$query->where('mobile', $opts['account'])->orWhere('email', $opts['account']);
			});
		} else if (!empty($opts['mobile'])) {
			$query->where('mobile', $opts['mobile']);
		} else if (!empty($opts['email'])) {
			$query->where('email', $opts['email']);
		} else if (!empty($opts['src_id'])) {
			$query->where('src_id', $opts['src_id']);
		}

		$acc = $query->first();
		if (!$acc) {
			$acc = Account::create(array_only($opts, ['name', 'nick_name', 'type', 'avatar', 'mobile', 'email', 'src_id', 'src_url', 'token', 'expire_time', 'info']));
		}
		$userAcc = UserAccount::where('account_id', $acc->id)->first();

		$user = false;
		if ($userAcc && $userAcc->user_id) {
			$user = User::find($userAcc->user_id);
		}
		if (!$user) {
			$query = User::where('type', $type);
			if (!empty($opts['user_id'])) {
				$query->where('id', $opts['user_id']);
			} else if (!empty($opts['account'])) {
				$query->where(function ($query) use ($opts) {
					$query->where('mobile', $opts['account'])->orWhere('email', $opts['account'])->orWhere('account', $opts['account']);
				});
			} else if (!empty($opts['mobile'])) {
				$query->where('mobile', $opts['mobile']);
			} else if (!empty($opts['email'])) {
				$query->where('email', $opts['email']);
			} else if (!empty($opts['src_id'])) {
				$query->where('id', $opts['src_id']);
			}
			$user = $query->first();
			if (!$user) {
				$data = array_only($opts, ['account', 'password', 'name', 'nick_name', 'email', 'mobile', 'type', 'avatar']);
				if (!empty($opts['user_id'])) {
					$data['id'] = $opts['user_id'];
				}
				if (!empty($data['password'])) {
					$data['secret'] = base64_encode($data['password']);
					$data['password'] = bcrypt($data['password']);
				}
				$user = User::create($data);
			}
			UserAccount::create(['user_id' => $user->id, 'account_id' => $acc->id]);
		}
		return $user;
	}
}
