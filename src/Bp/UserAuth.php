<?php

namespace Gmf\Sys\Bp;
use Auth;
use Carbon\Carbon;
use DB;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Controllers\Controller as BPListener;
use Gmf\Sys\Models;
use Gmf\Sys\Notifications;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Notification;
use Validator;

class UserAuth {
	public function checker(BPListener $observer, $input) {
		if (empty($input['account']) && empty($input['id'])) {
			throw new \Exception('参数错误！');
		}
		if (!empty($input['id'])) {
			$user = Models\User::find($input['id']);
		} else {
			$user = Models\User::where($input)->whereIn('type', ['sys', 'web'])->first();
		}
		if (!$user) {
			throw new \Exception('当前用户不存在!');
		}
		if (!empty($input['account']) && strlen($input['account']) != strlen($user->account)) {
			throw new \Exception('账号不匹配！');
		}
		if ($user->status_enum == 'locked') {
			throw new \Exception('当前账号可能被锁定!');
		}
		return $user;
	}
	public function login(BPListener $observer, $input) {
		Validator::make($input, [
			'account' => 'required|min:4|max:50',
			'password' => 'required|min:4|max:50',
		])->validate();

		$user = $this->checker($observer, array_only($input, ['id', 'account', 'email', 'type']));
		$input['account'] = $user->account;
		$credentials = array_only($input, ['account', 'password']);
		$credentials['type'] = $user->type;
		if (empty($credentials['account'])) {
			$credentials['id'] = $user->id;
		}
		if (Auth::attempt($credentials, true)) {
			if ($user->status_enum == 'locked') {
				throw new \Exception('当前账号可能被锁定!');
			}
			return $user;
		} else {
			throw new \Exception('密码不正确！');
		}
		return false;
	}
	public function logout(BPListener $observer, $input = []) {
		Auth::logout();
		return true;
	}
	public function register(BPListener $observer, $input) {
		Validator::make($input, [
			'name' => 'required',
			'account' => 'required|min:4|max:50',
			'password' => 'required|min:4|max:50',
		])->validate();
		$user = Models\User::whereIn('type', ['sys', 'web'])->where(function ($query) use ($input) {
			$query->where('account', $input['account'])
				->orWhere('mobile', $input['account'])
				->orWhere('email', $input['account']);
		})->first();
		if ($user) {
			throw new \Exception('该账号已经被使用了，请换个账号，或者直接登录!');
		}
		$input['client_id'] = config('gmf.client.id');
		$input['client_name'] = config('gmf.client.name');
		$user = Models\User::registerByAccount('web', $input);
		if (!$user) {
			throw new \Exception('创建账号失败!');
		}
		Auth::loginUsingId($user->id);
		return $user;
	}

	public function issueToken($user, $type = 'web') {
		$token = $user->createToken($type);
		$rtn = new Builder;
		$rtn->access_token($token->accessToken);
		$rtn->expires_in(strtotime($token->token->expires_at));
		$rtn->token_type('Bearer');
		return $rtn;
	}
	public function resetPassword(BPListener $observer, $input) {
		Validator::make($input, [
			'id' => 'required',
			'password' => 'required|confirmed|min:4|max:50',
			'token' => 'required',
		])->validate();

		$user = $this->checker($observer, $input);

		$vcode = ['id' => $user->id, 'type' => 'password', 'token' => $input['token']];

		$this->checkVCode($observer, $vcode);

		$user->password = Hash::make($input['password']);
		$user->setRememberToken(Str::random(60));
		$user->save();
		event(new PasswordReset($user));

		Auth::login($user);

		$this->deleteVCode($observer, $vcode);
		return $user;
	}
	public function createVCode(BPListener $observer, $input) {
		$code = '';
		for ($i = 0; $i < 6; $i++) {
			$code .= rand(0, 9);
		}
		Validator::make($input, [
			'id' => 'required', //用户ID
			'type' => 'required',
			'mode' => 'required|in:mail,phone',
		])->validate();

		$type = $input['type'];

		$user = app('Gmf\Sys\Bp\UserAuth')->checker($observer, array_only($input, ['id', 'account']));
		$vcode = Models\Auth\VCode::create([
			'user_id' => $input['id'],
			'type' => $input['type'],
			'token' => $code,
			'expire_time' => Carbon::now()->addMinutes(5),
		]);
		if ($input['mode'] == 'phone') {
			Notification::send([$user], new Notifications\PasswordResetSms($vcode));
		} else {
			if ($type == 'password') {
				Notification::send([$user], new Notifications\PasswordResetMail($vcode));
			} else if ($type == 'verify-mail') {
				Notification::send([$user], new Notifications\VerifyMail($vcode));
			}
		}
		return true;
	}
	public function deleteVCode(BPListener $observer, $input) {
		Validator::make($input, [
			'id' => 'required', //用户ID
			'type' => 'required',
			'token' => 'required',
		])->validate();
		Models\Auth\VCode::where([
			'user_id' => $input['id'],
			'type' => $input['type'],
			'token' => $input['token'],
		])->delete();
		return true;
	}
	public function checkVCode(BPListener $observer, $input) {
		Validator::make($input, [
			'id' => 'required', //用户ID
			'type' => 'required',
			'token' => 'required',
		])->validate();

		$t = Models\Auth\VCode::where([
			'user_id' => $input['id'],
			'type' => $input['type'],
			'token' => $input['token'],
		])->first();
		if (!$t) {
			throw new \Exception("验证码无效");
		}
		if ($t < Carbon::now()) {
			throw new \Exception("验证码已过期");
		}
		return true;
	}
	public function verifyMail(BPListener $observer, $user, $token) {
		try {
			DB::beginTransaction();
			$input = ['token' => $token, 'account' => $user->account, 'id' => $user->id, 'type' => 'verify-mail'];
			$this->checkVCode($observer, $input);

			Models\UserInfo::create(['user_id', 'type' => 'verify-mail', 'content' => json_encode(['email' => $user->email, 'token' => $token])]);
			$user->email_verified = true;
			Models\User::where('id', $user->id)->update(['email_verified' => 1]);

			$this->deleteVCode($observer, $input);

			DB::commit();
		} catch (Exception $e) {
			DB::rollBack();
			throw $e;
		}
		return true;
	}
}