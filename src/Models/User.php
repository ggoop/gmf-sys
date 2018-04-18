<?php

namespace Gmf\Sys\Models;
use Gmf\Passport\HasApiTokens;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Gmf\Sys\Uuid;
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
	public function routeNotificationForMail() {
		return $this->email;
	}
	public function validateForPassportPasswordGrant($password) {
		//var_dump($password);
		return true;
	}
	public static function registerByAccount($type, Array $opts = []) {
		$user = false;
		$opts['type'] = $type;

		Validator::make($opts, [
			'name' => 'required',
			'client_id' => 'required',
		])->validate();

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
			$opts['account'] = Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
		}
		$query = Account::where('type', $type)->where('client_id', $opts['client_id']);

		$query->where(function ($query) use ($opts) {
			$f = false;
			if (!empty($opts['account'])) {
				$f = true;
				$query->orWhere('mobile', $opts['account'])->orWhere('email', $opts['account']);
			}
			if (!empty($opts['mobile'])) {
				$f = true;
				$query->orWhere('mobile', $opts['mobile']);
			}
			if (!empty($opts['email'])) {
				$f = true;
				$query->orWhere('email', $opts['email']);
			}
			if (!empty($opts['src_id'])) {
				$f = true;
				$query->orWhere('src_id', $opts['src_id']);
			}
			if (!$f) {
				$query->where('id', 'xx==xx');
			}
		});
		$acc = $query->orderBy('created_at', 'desc')->first();
		if (!$acc) {
			$acc = Account::create(array_only($opts, [
				'client_id', 'client_type', 'client_name',
				'name', 'nick_name', 'type',
				'avatar', 'mobile', 'email', 'src_id', 'src_url', 'token', 'expire_time', 'info']));
		}
		$userAcc = UserAccount::where('account_id', $acc->id)->orderBy('is_default', 'desc')->first();

		$user = false;
		if ($userAcc && $userAcc->user_id) {
			$user = User::find($userAcc->user_id);
		}
		if (!$user) {
			$data = array_only($opts, ['account', 'password', 'name', 'nick_name', 'email', 'mobile', 'type', 'avatar']);
			if (!empty($opts['user_id']) && $type == 'sys') {
				$data['id'] = $opts['user_id'];
			}
			if (!empty($data['password']) && in_array($type, ['sys', 'web'])) {
				$data['secret'] = base64_encode($data['password']);
				$data['password'] = bcrypt($data['password']);
			} else {
				unset($data['secret']);
				unset($data['password']);
			}
			$user = User::create($data);

			UserAccount::updateOrCreate(['user_id' => $user->id, 'account_id' => $acc->id]);
		}
		return $user;
	}
}
