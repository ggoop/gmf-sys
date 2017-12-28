<?php

namespace Gmf\Sys\Bp;
use Auth;
use Carbon\Carbon;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Controllers\Controller as BPListener;
use Gmf\Sys\Models;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class UserAuth {
	public function checker(BPListener $observer, $input) {
		if (empty($input['account']) && empty($input['id'])) {
			throw new \Exception('参数错误！');
		}
		if (!empty($input['id'])) {
			$user = Models\User::find($input['id']);
		} else {
			$user = Models\User::where($input)->first();
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

		$user = $this->checker($observer, $input);
		$input['account'] = $user->account;
		$credentials = array_only($input, ['account', 'password']);
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
		$user = Models\User::where('account', $input['account'])
			->orWhere('mobile', $input['account'])
			->orWhere('email', $input['account'])
			->first();
		if ($user) {
			throw new \Exception('该账号已经被使用了，请换个账号，或者直接登录!');
		}
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
		$this->checkVCode($user, 'password', $input['token']);

		$user->password = Hash::make($input['password']);
		$user->setRememberToken(Str::random(60));
		$user->save();
		event(new PasswordReset($user));

		//Auth::logout();
		Auth::login($user);

		$this->deleteVCode($user);
		return $user;
	}
	public function createVCode($user, $type) {
		$code = '';
		for ($i = 0; $i < 6; $i++) {
			$code .= rand(0, 9);
		}
		Models\Auth\VCode::create([
			'user_id' => $user->id,
			'type' => $type,
			'token' => $code,
			'expire_time' => Carbon::now()->addMinutes(5),
		]);
		return $code;
	}
	public function deleteVCode($user, $type) {
		Models\Auth\VCode::where([
			'user_id' => $user->id,
			'type' => $type,
		])->delete();
		return true;
	}
	public function checkVCode($user, $type, $token) {
		$t = Models\Auth\VCode::where([
			'user_id' => $user->id,
			'type' => $type,
			'token' => $token,
		])->first();
		if (!$t) {
			throw new \Exception("验证码无效");
		}
		if ($t < Carbon::now()) {
			throw new \Exception("验证码已过期");
		}
		return true;
	}
}