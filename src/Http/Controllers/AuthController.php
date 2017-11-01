<?php

namespace Gmf\Sys\Http\Controllers;

use Auth;
use Gmf\Sys\Builder;
use Gmf\Sys\Models\User;
use Illuminate\Http\Request;

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
		$rtn->type('Bearer');

		return $this->toJson($rtn);
	}
}
