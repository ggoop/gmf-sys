<?php
namespace Gmf\Sys\Http\Controllers\Sv;

use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Gmf\Sys\Models;

class ConfigController extends Controller
{
  /**
   * 服务配置，授权
   * [{appId}]
   */
  public function config(Request $request)
  {
    $input = $request->all();
    Validator::make($input, [
      'packId' => 'required',
    ])->validate();
    $pack = Models\Sv\Pack::where('id', $input['packId'])->orWhere('openid', $input['packId'])->first();
    if (empty($pack)) {
      throw new \Exception('没有提供此项服务包.');
    }
    $entId = $request->input('entId', GAuth::entId());
    $token = app('Gmf\Sys\Bp\Sv\Config')->config([
      'packId' => $pack->id,
      'userId' => GAuth::id(),
      'entId' => $entId,
    ]);
    if (empty($token['host'])) {
      $token['host'] = $request->getSchemeAndHttpHost();
    }
    return $this->toJson($token);
  }
}
