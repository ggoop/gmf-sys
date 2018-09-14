<?php
namespace Gmf\Sys\Http\Controllers\Ent;

use DB;
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
    try {
      DB::beginTransaction();
      $ent->discover = $input['discover'];
      $ent->gateway = $input['gateway'];
      $ent->published = 1;
      $ent->save();

      //注册企业
      $params = [
        "token" => $input['token'],
        "type" => 'ent',
        'datas' => ['openid' => $ent->openid, 'name' => $ent->name, 'token' => $ent->token, 'gateway' => $input['gateway'], 'scope' => $ent->scope],
      ];
      $client = new GuzzleHttp\Client(['base_uri' => $input['discover']]);
      $res = $client->post('api/sys/ents/register', [
        'json' => $params,
        'headers' => array(
          'Content-Type' => 'application/json',
          'Accept' => 'application/json',
        ),
      ]);
      $body = (String) $res->getBody();
      if ($body) {
        $body = json_decode($body);
        $token = $body && $body->data ? $body->data : false;
      }
      DB::commit();
    } catch (\GuzzleHttp\Exception\ClientException $exception) {
      DB::rollBack();
      $error = $exception->getResponse()->getBody()->getContents();
      throw new \Exception(json_decode($error)->msg);
    } catch (\Exception $e) {
      DB::rollBack();
      throw $e;
    }
    return $this->toJson(true);
  }
}
