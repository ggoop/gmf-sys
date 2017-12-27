<?php

namespace Gmf\Sys\Bp;
use Auth;
use Gmf\Sys\Builder;
use Gmf\Sys\Http\Controllers\Controller as BPListener;
use Gmf\Sys\Models;
use Validator;

class UserAuth {
	public function checker(BPListener $observer, $input) {
		if (empty($input['account']) && empty($input['id'])) {
			throw new \Exception('参数错误！');
		}
		$user = Models\User::where($input)->first();
		if (!$user) {
			throw new \Exception('当前用户不存在!');
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

		$credentials = array_only($input, ['account', 'password']);

		$user = Models\User::where('account', $credentials['account'])->first();
		if (!$user) {
			throw new \Exception('当前用户不存在!');
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
		$user = Models\User::where('account', $input['account'])->first();
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
}
