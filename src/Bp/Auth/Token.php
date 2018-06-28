<?php

namespace Gmf\Sys\Bp\Auth;

use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Validator;
use Gmf\Sys\Bp\UserAuth;

class Token
{
  public function issueToken($user, $type = 'web')
  {
    $token = $user->createToken($type);
    $rtn = new Builder;
    $rtn->access_token($token->accessToken);
    $rtn->expires_in(strtotime($token->token->expires_at));
    $rtn->token_type('Bearer');
    return $rtn;
  }
  /**
   * 通过 用户 openid+ 应用 openid+ 企业 openid+ 企业应用 token 获取token
   * {token:{access_token:'',expires_in:'',token_type:'Bearer'},signature:'sss'}
   */
  public function issueTokenByOpenid($input)
  {
    // return [];
    Validator::make($input, [
      'userId' => 'required',
      'entId' => 'required',
      'token' => 'required'
    ])->validate();

    $user = Models\User::find($input['userId']);
    if (empty($user)) {
      throw new \Exception('没有此用户.');
    }
    $ent = Models\Ent\Ent::find($input['entId']);
    if (empty($ent)) {
      throw new \Exception('没有此企业.');
    }
    if ($ent->token != $input['token']) {
      throw new \Exception('token 授权失败!');
    }
    return $this->issueToken($user, 'ent_token');
  }
}