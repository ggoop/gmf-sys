<?php

namespace Gmf\Sys\Bp;
use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Validator;
class AppToken {
    /**
	 * 通过 用户 openid+ 应用 openid+ 企业 openid+ 企业应用 token 获取token
	 * {token:{access_token:'',expires_in:'',token_type:'Bearer'},signature:'sss'}
	 */
	public function issueToken($input) {
		Validator::make($input, [
			'user_openid' => 'required',
			'ent_openid' => 'required',
			'app_openid' => 'required',
			'token'=>'required'
        ])->validate();
        $user=Models\User::where('openid',$input['user_openid'])->first();
        if(empty($user)){
            throw new \Exception('没有此用户.');
        }
        $ent=Models\Ent::where('openid',$input['ent_openid'])->first();
        if(empty($ent)){
            throw new \Exception('没有此企业.');
        }
		if($ent->token!=$input['token']){
			throw new \Exception('token 授权失败!');
		}
		return app('Gmf\Sys\Bp\UserAuth')->issueToken($user, 'app_token');
	}
	public function parse($id) {
		$c = Models\Scode::find($id);
		if (empty($c)) {
			return false;
		}
		return unserialize($c->content);
	}
}