<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Passport\HasApiTokens;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Uuid;
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
	protected $fillable = [
		'id', 'account', 'mobile', 'email', 'password', 'secret',
		'name', 'nick_name', 'gender',
		'type', 'cover', 'avatar', 'titles', 'memo', 'status_enum',
		'client_id', 'client_type', 'client_name', 'src_id', 'src_url', 'info',
	];

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
	public function routeNotificationForMail() {
		return $this->email;
	}
	public function validateForPassportPasswordGrant($password) {
		//var_dump($password);
		return true;
	}
	public static function findByAccount($account, $type, Array $opts = []) {
		if (empty($account) || empty($type)) {
			return false;
		}

		if (empty($opts)) {
			$opts = [];
		}

		$opts['account'] = $account;
		$opts['type'] = $type;
		return static::where($opts)->first();
	}
	public static function registerByAccount($type, Array $opts = []) {
		$user = false;
		$opts['type'] = $type;

		Validator::make($opts, [
			'name' => 'required',
		])->validate();
		if ($type != 'sys') {
			Validator::make($opts, [
				'client_id' => 'required',
			])->validate();
		}

		if (empty($opts['account']) && empty($opts['email']) && empty($opts['mobile']) && empty($opts['src_id'])) {
			throw new \Exception('account,email ,mobile,src_id not empty least one');
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
		if (empty($opts['account'])) {
			$opts['account'] = Uuid::generate();
		}
		$query = static::where('type', $type);
		if (!in_array($opts['type'], ['sys', 'web'])) {
			$query->where('client_id', $opts['client_id']);
		}
		$query->where(function ($query) use ($opts) {
			$f = false;
			if (!empty($opts['account'])) {
				$f = true;
				$query->orWhere('mobile', $opts['account'])
					->orWhere('email', $opts['account'])
					->orWhere('account', $opts['account']);
			}
			if (!empty($opts['mobile'])) {
				$f = true;
				$query->orWhere('mobile', $opts['mobile']);
			}
			if (!empty($opts['email'])) {
				$f = true;
				$query->orWhere('email', $opts['email']);
			}
			if (!empty($opts['src_id']) && !in_array($opts['type'], ['sys', 'web'])) {
				$f = true;
				$query->orWhere('src_id', $opts['src_id']);
			}
			if (!$f) {
				$query->where('id', 'xx==xx');
			}
		});
		$user = $query->orderBy('created_at', 'desc')->first();

		$data = array_only($opts, [
			'id', 'account', 'mobile', 'email', 'password', 'secret',
			'name', 'nick_name', 'gender',
			'type', 'cover', 'avatar', 'titles', 'memo',
			'client_id', 'client_type', 'client_name', 'src_id', 'src_url', 'info',
		]);
		if ($user) {
			$updates = [];
			if (empty($user->mobile) && !empty($data['mobile']) && !in_array($type, ['sys', 'web'])) {
				$updates['mobile'] = $data['mobile'];
			}
			if (empty($user->email) && !empty($data['email']) && !in_array($type, ['sys', 'web'])) {
				$updates['email'] = $data['email'];
			}
			if (empty($user->gender) && !empty($data['gender'])) {
				$updates['gender'] = $data['gender'];
			}
			if (empty($user->titles) && !empty($data['titles'])) {
				$updates['titles'] = $data['titles'];
			}
			if (empty($user->memo) && !empty($data['memo'])) {
				$updates['memo'] = $data['memo'];
			}
			if (count($updates) > 0) {
				User::where('id', $user->id)->update($updates);
			}
		} else {
			if (!empty($data['password']) && in_array($type, ['sys', 'web'])) {
				$data['secret'] = base64_encode($data['password']);
				$data['password'] = bcrypt($data['password']);
			} else {
				unset($data['secret']);
				unset($data['password']);
			}
			$user = User::create($data);
		}
		return $user;
	}
	public function linkUser($user) {
		if ($this->account == $user->account) {
			return false;
		}
		$credentials = ['fm_user_id' => $this->id, 'to_user_id' => $user->id];
		UserLink::updateOrCreate($credentials);
		return true;
	}
}
