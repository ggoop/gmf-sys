<?php

namespace Gmf\Sys\Bp\Auth;

use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Validator;
use Gmf\Sys\Bp\UserAuth;
use Zend\Diactoros\Response as Psr7Response;
use Zend\Diactoros\ServerRequest as Psr7ServerRequest;
use League\OAuth2\Server\AuthorizationServer;
use DB;

class Token
{
  public function issueClientToken($input = [])
  {
    Validator::make($input, [
      'client_id' => 'required',
      'client_secret' => 'required',
    ])->validate();
    $input['grant_type'] = 'client_credentials';
    $token = app(AuthorizationServer::class)->respondToAccessTokenRequest(app(Psr7ServerRequest::class)->withParsedBody($input), new Psr7Response);
    $token = \json_decode($token->getBody()->__toString());
    $rtn = new Builder;
    $rtn->access_token($token->access_token);
    $rtn->expires_in($token->expires_in);
    $rtn->token_type($token->token_type);
    return $rtn;
  }
  public function issueCodeToken($input = [])
  {
    Validator::make($input, [
      'client_id' => 'required',
      'code' => 'required',
      'client_secret' => 'required',
    ])->validate();
    $code = $input['code'];
    $client_id = $input['client_id'];
    $client_secret = $input['client_secret'];
    if ($c = app('Gmf\Sys\Bp\VCode')->checker($code, 'oauth.authorize')) {
      if (!DB::table('gmf_oauth_clients')->where('id', $client_id)->where('secret', $client_secret)->exists()) {
        throw new \Exception('应用无效!');
      }
      if ($c->client_id != $client_id) {
        throw new \Exception('授权码被篡改!');
      }
      app('Gmf\Sys\Bp\VCode')->delete($c->id);
      $user = Models\User::find($c->user_id);
      if (empty($user)) {
        throw new \Exception('没有此用户.');
      }
      return $this->issueToken($user, 'authorize.token');
    }
    throw new \Exception('授权码无效!');
  }
  public function issueToken($user, $type = 'web')
  {
    $token = $user->createToken($type);
    $rtn = new Builder;
    $rtn->access_token($token->accessToken);
    $rtn->expires_in(strtotime($token->token->expires_at));
    $rtn->token_type('Bearer');
    $rtn->openid($user->openid ? : $user->id);
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