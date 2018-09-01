<?php
namespace Gmf\Sys\Http\Controllers\Ent;

use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Models;
use GuzzleHttp;
use Illuminate\Http\Request;
use Validator;

class PublishController extends Controller {
  /**
   * 提供企业应用注册服务
   * 注册企业
   */
  public function publish(Request $request) {
    $input = $request->all();
    Validator::make($input, [
      'token' => 'required',
      'account' => 'required',
      'discover' => 'required',
    ])->validate();
    if (empty($input['gateway'])) {
      $input['gateway'] = $request->getSchemeAndHttpHost();
    }
    $entId = GAuth::entId();
    if (empty($entId)) {
      throw new \Exception('需要进入企业环境，才能发布！');
    }
    $ent = Models\Ent\Ent::find($entId);
    if (empty($ent)) {
      throw new \Exception('没有企业信息，不能发布!');
    }
    $ent->discover = $input['discover'];
    $ent->gateway = $input['gateway'];
    $ent->published = 1;
    $ent->save();
    //注册企业
    $params = [
      "token" => $input['token'],
      "account" => $input['account'],
      "type" => 'ent',
      'datas' => ['openid' => $ent->openid, 'name' => $ent->name, 'token' => $ent->token, 'gateway' => $input['gateway'], 'scope' => $ent->scope],
    ];
    $client = new GuzzleHttp\Client(['base_uri' => $input['discover']]);
    $res = $client->post('api/sys/ents/register', ['json' => $params]);
    $body = (String) $res->getBody();
    if ($body) {
      $body = json_decode($body);
      $token = $body && $body->data ? $body->data : false;
    }
    return $this->toJson(true);
  }
}
