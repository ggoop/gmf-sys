<?php

namespace Gmf\Sys\Bp;

use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Validator;

class AppToken
{
  /**
   * 通过 用户 openid+ 应用 openid+ 企业 openid+ 企业应用 token 获取token
   * {token:{access_token:'',expires_in:'',token_type:'Bearer'},signature:'sss'}
   */
  public function issueToken($input)
  {
    // return [];
    Validator::make($input, [
      'userId' => 'required',
      'entId' => 'required',
      'appId' => 'required',
      'token' => 'required'
    ])->validate();
    
    $user = Models\User::find($input['userId']);
    if (empty($user)) {
      throw new \Exception('没有此用户.');
    }
    $ent = Models\Ent::find($input['entId']);
    if (empty($ent)) {
      throw new \Exception('没有此企业.');
    }
    if ($ent->token != $input['token']) {
      throw new \Exception('token 授权失败!');
    }
    return (new UserAuth)->issueToken($user, 'app_token');
  }
  public function parse($id)
  {
    $c = Models\Scode::find($id);
    if (empty($c)) {
      return false;
    }
    return unserialize($c->content);
  }
}