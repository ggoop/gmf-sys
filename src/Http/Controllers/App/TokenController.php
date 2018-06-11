<?php
namespace Gmf\Sys\Http\Controllers\App;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GAuth;
use Validator;
class TokenController extends Controller {
	/**
	 * 通过 用户 openid+ 应用 openid+ 企业 openid+ 企业应用 token 获取token
	 * {token:{access_token:'',expires_in:'',token_type:'Bearer'},signature:'sss'}
	 */
	public function token(Request $request) {
		$token=app('Gmf\Sys\Bp\AppToken')->issueToken($request->all());		
		return $this->toJson(['token'=>$token]);
	}
}
