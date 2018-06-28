<?php

namespace Gmf\Sys\Bp\Sv;

use GAuth;
use Gmf\Sys\Models;
use GuzzleHttp;
use Validator;
use Gmf\Sys\Bp\Auth\Token;
class Config
{
  /**
   * 通过 用户 openid+ 应用 openid+ 企业 openid+ 企业应用 token 获取token
   * {token:{access_token:'',expires_in:'',token_type:'Bearer'},signature:'sss'}
   */
  public function config($input)
  {
    Validator::make($input, [
      'packId' => 'required',
      'entId' => 'required',
      'userId' => 'required',
    ])->validate();
    $user = Models\User::find($input['userId']);
    if (empty($user)) {
      throw new \Exception('没有此用户.');
    }
    $ent = Models\Ent\Ent::find($input['entId']);
    if (empty($ent)) {
      throw new \Exception('没有此企业.');
    }
    $pack = Models\Sv\Pack::find($input['packId']);
    if (empty($app)) {
      throw new \Exception('没有此服务包.');
    }
    $gateway =$ent->gateway;

    $token = false;
    if (empty($gateway)) {
      //没有网关时，用本地服务
      $params = [
        "userId" => $user->id,
        "entId" => $ent->id,
        'token' => $ent->token,
      ];
      $token = (new Token())->issueTokenByOpenid($params);
    } else {
      //远程服务授权
      $params = [
        "user_openid" => $user->openid,
        "ent_openid" => $ent->openid,
        'token' => $ent->token,
        'type' => 'ent',
      ];
      $client = new GuzzleHttp\Client(['base_uri' => $gateway]);
      $res = $client->post('api/sys/auth/token', ['json' => $params]);
      $body = (String)$res->getBody();
      if ($body) {
        $body = json_decode($body);
        $token = $body && $body->data ? $body->data : false;
      }
    }
    $result = [
      'host' => $gateway,
      'ent' => $ent->openid,
      'token' => $token,
    ];
    return $result;
  }
}
