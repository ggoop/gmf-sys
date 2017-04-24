<?php

namespace Gmf\Sys\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Validator;

class AuthController extends Controller {
	public function getLogin(Request $request) {
		return view('gmf::auth.login');
	}
	public function postLogin(Request $request) {
		$this->validate($request, ['account' => 'required|min:4|max:50', 'secret' => 'required|min:4|max:50']);
		$credentials = $request->only('account', 'secret');
		$u = \App\Models\User::where('account', $credentials['account'])->first();
		if (!$u) {
			return redirect()->back()
				->withInput($request->only('account'))
				->withErrors(['account' => '当前用户不存在!']);
		}
		if ($u->secret != $credentials['secret']) {
			return redirect()->back()
				->withInput($request->only('account'))
				->withErrors(['secret' => '密码可能错了!']);
		}
		return $this->login($request, $u->id);
	}
	public function getLogout(Request $request) {
		Auth::logout();
		return redirect('/');
	}
	public function getRegister(Request $request) {
		return view('gmf::auth.register');
	}
	public function postRegister(Request $request) {
		$this->validate($request, ['account' => 'required|min:4|max:50', 'secret' => 'required|min:4|max:50|confirmed']);
		$credentials = $request->only('account', 'secret');
		$u = \App\Models\User::where('account', $credentials['account'])->first();
		if ($u) {
			return redirect()->back()
				->withInput($request->only('account'))
				->withErrors(['account' => '当前账号已经被注册了!']);
		}
		$u = $this->createUser($request, $credentials);
		return $this->login($request, $u->id);
	}
	public function getEmail(Request $request) {
		return view('gmf::auth.email');
	}
	public function postEmail(Request $request) {
		$this->validate($request, ['email' => 'required|email|min:4|max:50']);
	}
	public function getReset(Request $request, $token = null) {
		return view('gmf::auth.reset')->with(['token' => $token, 'email' => $request->email]);
	}
	public function postReset(Request $request) {

	}
	public function broker() {
		return Password::broker();
	}
	private function createUser(Request $request, $credentials) {
		$this->validate($request, ['account' => 'required|min:4|max:50']);
		$uid = SID::generate();
		$credentials['id'] = $uid;
		if (!isset($credentials['avatar']) || !$credentials['avatar']) {
			$credentials['avatar'] = '/img/avatar/' . mt_rand(1, 50) . '.jpg';
		}
		if (!isset($credentials['nickname'])) {
			$credentials['nickname'] = $credentials['account'];
		}
		if (!isset($credentials['secret'])) {
			$credentials['secret'] = '123987';
		}
		if (!isset($credentials['email'])) {
			if (Validator::make($credentials, ['account' => 'required|email'])->passes()) {
				$credentials['email'] = $credentials['account'];
			}
		}
		$user = \App\Models\User::create($credentials);
		return $user;
	}
	private function login(Request $request, $user_id) {
		$user = \App\Models\User::find($user_id);
		if (!$user) {
			return redirect()->back()
				->withInput($request->only('account', 'secret'))
				->withErrors(['account' => '当前账号异常,没有找到用户信息!']);
		}
		if ($user->status != 0) {
			return redirect()->back()
				->withInput($request->only('account', 'secret'))
				->withErrors(['account' => '当前账号可能被锁定!']);
		}
		Auth::loginUsingId($user->id, true);
		$callback = '/';
		if ($request->has('callback')) {
			$callback = $request->get('callback');
		}
		return redirect()->intended($callback);
	}
}
