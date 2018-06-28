<?php
namespace Gmf\Sys\Http\Controllers\App;

use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GAuth;
use Validator;
use Gmf\Sys\Models;
class TokenController extends Controller
{
  /**
   * 通过 用户 openid+ 应用 openid+ 企业 openid+ 企业应用 token 获取token
   * {token:{access_token:'',expires_in:'',token_type:'Bearer'},signature:'sss'}
   */
  public function token(Request $request)
  {
    $input = $request->all();
    Validator::make($input, [
      'app_openid' => 'required',
      'ent_openid' => 'required',
      'user_openid' => 'required',
      'token' => 'required',
    ])->validate();

    $params = [];
    $app = Models\App\App::where('openid', $input['app_openid'])->first();
    if (!empty($app)) {
      $params['appId'] = $app->id;
    }
    $ent = Models\Ent\Ent::where('openid', $input['ent_openid'])->first();
    if (!empty($ent)) {
      $params['entId'] = $ent->id;
    }
    $user = Models\User::where('openid', $input['user_openid'])->first();
    if (!empty($user)) {
      $params['userId'] = $user->id;
    }
    $params['token']=$input['token'];
    $token = app('Gmf\Sys\Bp\AppToken')->issueToken($params);
    return $this->toJson($token);
  }
}
