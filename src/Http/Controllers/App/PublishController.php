<?php
namespace Gmf\Sys\Http\Controllers\App;

use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Models;
use GuzzleHttp;
use Illuminate\Http\Request;
use Validator;

class PublishController extends Controller
{
    /**
     * 提供企业应用注册服务
     * 注册企业
     */
    public function publish(Request $request)
    {
        $input = $request->all();
        Validator::make($input, [
            'token' => 'required',
            'account' => 'required',
            'discover' => 'required',
				])->validate();
				$input['gateway']=$request->getSchemeAndHttpHost();

        $entId = GAuth::entId();
        if (empty($entId)) {
            throw new \Exception('需要进入企业环境，才能发布！');
        }
        $ent = Models\Ent::find($entId);
        if (empty($ent)) {
            throw new \Exception('没有企业信息，不能发布!');
        }
        $params = [
            "token" =>$input['token'],
            "account" => $input['account'],
            "gateway" =>$input['gateway'],
            'datas' => ['openid' => $ent->openid, 'name' => $ent->name,'token'=>$ent->token],
        ];
        //远程服务授权
        $client = new GuzzleHttp\Client(['base_uri' => $input['discover']]);
        $res = $client->post('api/sys/apps/register-ent', ['json' => $params]);
        $body = (String) $res->getBody();
        if ($body) {
            $body = json_decode($body);
            $token = $body && $body->data ? $body->data : false;
        }
        return $this->toJson(true);
    }
}
