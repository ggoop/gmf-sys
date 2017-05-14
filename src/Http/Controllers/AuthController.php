<?php

namespace Gmf\Sys\Http\Controllers;

use Auth;
use Gmf\Sys\Models;
use Gmf\Sys\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller {
	protected $redirectTo = '/';

	public function __construct() {
		$this->redirectTo = config('gmf.auth_redirect', $this->redirectTo);
	}
	public function getLogin(Request $request) {
		if (Auth::check()) {
			$callback = $this->redirectTo;
			if ($request->has('callback')) {
				$callback = $request->get('callback');
			}
			return redirect()->intended($callback);
		}
		$params = $request->intersect(['callback']);
		return view('gmf::auth.login', compact('params'));
	}
	public function postLogin(Request $request) {
		$this->validate($request, ['account' => 'required|min:4|max:50', 'password' => 'required|min:4|max:50']);
		$credentials = $request->only('account', 'password');
		$u = Models\User::where('account', $credentials['account'])->first();
		if (!$u) {
			return redirect()->back()
				->withInput($request->only('account'))
				->withErrors(['account' => '当前用户不存在!']);
		}
		if (Auth::attempt($credentials)) {
			return $this->login($request, $u->id);
		} else {
			return redirect()->back()
				->withInput($request->only('account', 'callback'))
				->withErrors(['password' => '密码可能错了!']);
		}
	}
	public function getLogout(Request $request) {
		Auth::logout();

		$callback = $this->redirectTo;
		if ($request->has('callback')) {
			$callback = $request->get('callback');
		}
		return redirect($callback);
	}
	public function getRegister(Request $request) {
		$params = $request->intersect(['callback']);
		return view('gmf::auth.register', compact('params'));
	}
	public function postRegister(Request $request) {
		$this->validate($request, ['account' => 'required|min:4|max:50', 'password' => 'required|min:4|max:50|confirmed']);
		$credentials = $request->only('account', 'password');
		$u = Models\User::where('account', $credentials['account'])->first();
		if ($u) {
			return redirect()->back()
				->withInput($request->only('account', 'callback'))
				->withErrors(['account' => '当前账号已经被注册了!']);
		}
		$this->validate($request, ['account' => 'required|min:4|max:50']);

		$u = User::registerByAccount('web', $credentials);

		return $this->login($request, $u->id);
	}
	public function getEmail(Request $request) {
		$params = $request->intersect(['callback']);
		return view('gmf::auth.email', compact('params'));
	}
	public function postEmail(Request $request) {
		$this->validate($request, ['email' => 'required|email|min:4|max:50']);
	}
	public function getReset(Request $request, $token = null) {
		$params = $request->intersect(['callback']);
		return view('gmf::auth.reset', compact('params'))->with(['token' => $token, 'email' => $request->email]);
	}
	public function postReset(Request $request) {

	}
	public function getToken(Request $request) {
		$token = false;
		if (Auth::check()) {
			$user = Models\User::find(Auth::id());
			if ($user) {
				$token = $user->createToken('web')->accessToken;
			}
		}
		return $token;
	}
	public function broker() {
		return Password::broker();
	}
	private function login(Request $request, $user_id) {
		$user = Models\User::find($user_id);
		if (!$user) {
			return redirect()->back()
				->withInput($request->only('account', 'password'))
				->withErrors(['account' => '当前账号异常,没有找到用户信息!']);
		}
		if ($user->status_enum == 'locked') {
			return redirect()->back()
				->withInput($request->only('account', 'password'))
				->withErrors(['account' => '当前账号可能被锁定!']);
		}
		Auth::loginUsingId($user->id, true);
		$callback = $this->redirectTo;
		if ($request->has('callback')) {
			$callback = $request->get('callback');
		}
		return redirect()->intended($callback);
	}
}
