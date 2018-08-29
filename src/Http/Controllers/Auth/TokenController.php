<?php
namespace Gmf\Sys\Http\Controllers\Auth;

use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Models;
use Validator;
use Illuminate\Http\Request;

class TokenController extends Controller
{
  public function issueToken(Request $request)
  {
    $token = false;
    $type = $request->input('type', 'password');
    switch ($type) {
      case 'password':
        $token = $this->issueTokenByPassword($request->all());
        break;
      case 'ent':
        $token = $this->issueTokenByOpenid($request->all());
        break;
      case 'client_credentials':
        $token = app('Gmf\Sys\Bp\Auth\Token')->issueClientToken($request->all());
        break;
      case 'authorization_code':
        $token = app('Gmf\Sys\Bp\Auth\Token')->issueCodeToken($request->all());
        break;
    }
    return $this->toJson($token);
  }
  private function issueTokenByPassword($input)
  {
    $input = array_only($input, ['id', 'account', 'password']);
    Validator::make($input, [
      'account' => [
        'required',
      ],
      'password' => [
        'required',
      ],
    ])->validate();
    $user = app('Gmf\Sys\Bp\UserAuth')->login($this, $input);
    return app('Gmf\Sys\Bp\Auth\Token')->issueToken($user);
  }
  /**
   * 通过 用户 openid+ 应用 openid+ 企业 openid+ 企业应用 token 获取token
   * {token:{access_token:'',expires_in:'',token_type:'Bearer'},signature:'sss'}
   */
  private function issueTokenByOpenid($input)
  {
    Validator::make($input, [
      'ent_openid' => 'required',
      'user_openid' => 'required',
      'token' => 'required',
    ])->validate();

    $params = [];
    $ent = Models\Ent\Ent::where('openid', $input['ent_openid'])->first();
    if (!empty($ent)) {
      $params['entId'] = $ent->id;
    }
    $user = Models\User::where('openid', $input['user_openid'])->first();
    if (!empty($user)) {
      $params['userId'] = $user->id;
    }
    $params['token'] = $input['token'];
    return app('Gmf\Sys\Bp\Auth\Token')->issueTokenByOpenid($params);
  }
}
