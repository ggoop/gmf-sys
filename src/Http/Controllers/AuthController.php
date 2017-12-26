<?php

namespace Gmf\Sys\Http\Controllers;

use Auth;
use Gmf\Sys\Builder;
use Illuminate\Http\Request;
use Validator;

class AuthController extends Controller {

	public function issueToken(Request $request) {
		$token = false;
		$input = array_only($request->all(), ['account', 'password']);
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
		if (!Auth::attempt($input)) {
			return $this->toError('授权失败!');
		}
		$token = Auth::user()->createToken('web');
		$rtn = new Builder;
		$rtn->access_token($token->accessToken);
		$rtn->expires_in(strtotime($token->token->expires_at));
		$rtn->token_type('Bearer');
		return $this->toJson($rtn);
	}
	public function issueLogin(Request $request) {
		$token = false;
		$input = array_only($request->all(), ['account', 'password']);
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
		if (!Auth::attempt($input, true)) {
			return $this->toError('授权失败!');
		}
		$token = Auth::user()->createToken('web');
		$rtn = new Builder;
		$rtn->access_token($token->accessToken);
		$rtn->expires_in(strtotime($token->token->expires_at));
		$rtn->token_type('Bearer');

		return $this->toJson($rtn);
	}
	public function issueLogout(Request $request) {
		Auth::logout();
		return $this->toJson(true);
	}
}
