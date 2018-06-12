<?php

namespace Gmf\Sys\Bp;

use GAuth;
use Gmf\Sys\Models;
use GuzzleHttp;
use Validator;
class AppConfig
{
    /**
     * 通过 用户 openid+ 应用 openid+ 企业 openid+ 企业应用 token 获取token
     * {token:{access_token:'',expires_in:'',token_type:'Bearer'},signature:'sss'}
     */
    public function config($input)
    {
        Validator::make($input, [
            'appId' => 'required',
            'entId' => 'required',
            'userId' => 'required',
        ])->validate();
        $user = Models\User::find($input['userId']);
        if (empty($user)) {
            throw new \Exception('没有此用户.');
        }
        $ent = Models\Ent::find($input['entId']);
        if (empty($ent)) {
            throw new \Exception('没有此企业.');
        }
        $app = Models\App\App::where('openid', $input['appId'])->orWhere('id', $input['appId'])->first();
        if (empty($app)) {
            throw new \Exception('没有此应用.');
        }
        $entApp = Models\App\EntApp::where('ent_id', $ent->id)->where('app_id', $app->id)->first();
        // if (empty($entApp)) {
        //     throw new \Exception('企业没有配置应用!');
        // }
        // $entUser = Models\EntUser::where('ent_id', $ent->id)->where('user_id', $app->id)->first();
        // if (empty($entApp)) {
        //     throw new \Exception('企业没有配置用户!');
        // }
        $gateway = ($entApp&&$entApp->gateway) ?$entApp->gateway: $ent->gateway;

        $params = [
            "user_openid" => $user->openid,
            "ent_openid" => $ent->openid,
            "app_openid" => $app->openid,
            'token' => $ent->token,
        ];
        $token = false;
        if (empty($gateway)) {
            //没有网关时，用本地服务
            $token = app('Gmf\Sys\Bp\AppToken')->issueToken($params);
        } else {
            //远程服务授权
            $client = new GuzzleHttp\Client(['base_uri' => $gateway]);
            $res = $client->post('api/sys/apps/token', ['json' => $params]);
            $body = (String) $res->getBody();
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