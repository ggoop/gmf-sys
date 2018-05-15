<?php

namespace Gmf\Sys\Http\Controllers;
use Auth;
use DB;
use GAuth;
use Gmf\Sys\Http\Resources;
use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Validator;

class AuthController extends Controller {
	public function checker(Request $request) {
		return $this->toJson(new Resources\User(app('Gmf\Sys\Bp\UserAuth')->checker($this, $request->only('account', 'id'))));
	}
	public function getLogged(Request $request) {
		$user = GAuth::user();
		return $this->toJson(new Resources\User($user), function ($b) use ($user) {
			if ($user) {
				$token = app('Gmf\Sys\Bp\UserAuth')->issueToken($user);
				$b->token($token);
			}
		});
	}
	public function getUser(Request $request) {
		return $this->toJson(new Resources\User(app('Gmf\Sys\Bp\UserAuth')->checker($this, $request->only('account', 'id'))));
	}
	public function loginWithVCode(Request $request, $vcode = '') {
		if ($c = app('Gmf\Sys\Bp\VCode')->checker($vcode, 'auth.login')) {
			app('Gmf\Sys\Bp\VCode')->delete($vcode);
			if ($c->user_id) {
				$user = Models\User::find($c->user_id);
				if ($user && Auth::loginUsingId($user->id)) {
					$token = app('Gmf\Sys\Bp\UserAuth')->issueToken($user);
					return $this->toJson(new Resources\User($user), function ($b) use ($token) {
						$b->token($token);
					});
				}
			}
		}
		throw new \Exception("code 登录失败");
	}
	public function login(Request $request) {
		$input = $request->only('id', 'account', 'password');
		try {
			DB::beginTransaction();

			$user = app('Gmf\Sys\Bp\UserAuth')->login($this, $input);
			$token = false;
			if ($user) {
				$token = app('Gmf\Sys\Bp\UserAuth')->issueToken($user);
			} else {
				throw new \Exception("登录失败!");
			}
			DB::commit();

			return $this->toJson(new Resources\User($user), function ($b) use ($token) {
				$b->token($token);
			});
		} catch (Exception $e) {
			DB::rollBack();
			throw $e;
		}
	}
	public function register(Request $request) {
		$input = $request->all();
		Validator::make($input, [
			'name' => 'required',
			'account' => 'required|min:4|max:50',
			'password' => 'required|min:4|max:50',
		])->validate();
		try {
			DB::beginTransaction();

			$user = app('Gmf\Sys\Bp\UserAuth')->register($this, $input);
			$token = false;
			if ($user) {
				$token = app('Gmf\Sys\Bp\UserAuth')->issueToken($user);
			} else {
				throw new \Exception("注册失败!");
			}
			DB::commit();
			return $this->toJson(new Resources\User($user), function ($b) use ($token) {
				$b->token($token);
			});
		} catch (Exception $e) {
			DB::rollBack();
			throw $e;
		}
	}
	public function issueToken(Request $request) {
		$token = false;
		$input = array_only($request->all(), ['id', 'account', 'password']);
		$validator = Validator::make($input, [
			'account' => [
				'required',
			],
			'password' => [
				'required',
			],
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$user = app('Gmf\Sys\Bp\UserAuth')->login($this, $input);

		$token = app('Gmf\Sys\Bp\UserAuth')->issueToken($user);
		return $this->toJson($token);
	}
	public function logout(Request $request) {
		try {
			DB::beginTransaction();
			app('Gmf\Sys\Bp\UserAuth')->logout($this);
			DB::commit();
			$this->toJson(true);
		} catch (Exception $e) {
			DB::rollBack();
			throw $e;
		}
	}
	public function entryEnt(Request $request, $id) {
		$kv = [];
		$ent = Models\Ent::find($id);
		if ($ent) {
			$kv[GAuth::SESSION_ENT_KEY()] = $ent->id;
			session($kv);
			return $this->toJson($ent);
		}
		return $this->toJson(false);
	}
	public function createVCode(Request $request) {
		$input = array_only($request->all(), ['type', 'account', 'id', 'mode']);
		Validator::make($input, [
			'id' => 'required', //用户ID
			'type' => 'required',
			'mode' => 'required|in:mail,sms',
		])->validate();

		app('Gmf\Sys\Bp\UserAuth')->createVCode($this, $input);
		$this->toJson(true);
	}
	public function checkVCode(Request $request) {
		$input = array_only($request->all(), ['token', 'account', 'id', 'type']);
		app('Gmf\Sys\Bp\UserAuth')->checkVCode($this, $input);
		$this->toJson(true);
	}

	public function resetPassword(Request $request) {
		$input = array_only($request->all(), [
			'password', 'password_confirmation', 'token', 'account', 'id',
		]);
		$user = app('Gmf\Sys\Bp\UserAuth')->resetPassword($this, $input);

		$token = app('Gmf\Sys\Bp\UserAuth')->issueToken($user);

		return $this->toJson(new Resources\User($user), function ($b) use ($token) {
			$b->token($token);
		});
	}

	public function verifyMail(Request $request) {
		$input = array_only($request->all(), ['token', 'account', 'id']);

		Validator::make($input, [
			'id' => 'required',
			'token' => 'required',
			'account' => 'required',
		])->validate();

		$user = app('Gmf\Sys\Bp\UserAuth')->checker($this, $request->only('account', 'id'));
		if (!$user || $input['account'] != $user->account || $input['id'] != $user->id) {
			throw new \Exception("非法用户...");
		}
		$this->toJson(app('Gmf\Sys\Bp\UserAuth')->verifyMail($this, $user, $input['token']));
	}
	public function addJoins(Request $request) {
		GAuth::check();
		$input = array_only($request->all(), ['account', 'password']);
		$validator = Validator::make($input, [
			'account' => ['required'],
			'password' => ['required'],
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$user = app('Gmf\Sys\Bp\UserAuth')->checker($this, array_only($input, ['account']));
		if (GAuth::user()->account == $user->account) {
			throw new \Exception("不能关联自己...");
		}
		if (!empty($user->password) && bcrypt($input['password']) != $user->password) {
			GAuth::user()->linkUser($user);
			return $this->toJson(new Resources\User($user));
		} else {
			throw new \Exception("密码不正确...");
		}
	}
	public function getJoins(Request $request) {
		GAuth::check();
		return $this->toJson((new Resources\UserCollection(Models\User::whereIn('id', GAuth::ids())->where('id', '!=', GAuth::id())->get()))->withCallback(function ($rtn, $value) {
			$rtn->account($value->account);
		}));
	}
	public function removeJoins(Request $request) {
		GAuth::check();
		$uids = $request->input('to_user_id');
		if (!empty($uids) && is_string($uids)) {
			$uids = explode(',', $uids);
		}
		if (is_array($uids)) {
			Models\UserLink::where('fm_user_id', GAuth::id())->whereIn('to_user_id', $uids)->delete();
			Models\UserLink::where('to_user_id', GAuth::id())->whereIn('fm_user_id', $uids)->delete();
		}
		return $this->toJson(true);
	}
}
