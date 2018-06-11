<?php
namespace Gmf\Sys\Http\Controllers\App;

use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GAuth;
use Validator;
use GuzzleHttp;

class ConfigController extends Controller
{
	/**
	 * 应用服务配置，授权
	 * [{user_openid,app_openid,ent_openid}]
	 */
	public function config(Request $request)
	{
		Validator::make($input, [
			'appId' => 'required'
		])->validate();
		$user = GAuth::user();
		if (empty($user)) {
			throw new \Exception('没有此用户.');
		}
		$ent = GAuth::ent();
		if (empty($ent)) {
			throw new \Exception('没有此企业.');
		}
		$app = Models\App\App::where('openid', $input['appId'])->orWhere('id',$input['appId'])->first();
		if (empty($app)) {
			throw new \Exception('没有此应用.');
		}
		$entApp = Models\App\EntApp::where('ent_id', $ent->id)->where('app_id', $app->id)->first();
		if (empty($entApp)) {
			throw new \Exception('企业没有配置应用!');
		}
		$entUser = Models\EntUser::where('ent_id', $ent->id)->where('user_id', $app->id)->first();
		if (empty($entApp)) {
			throw new \Exception('企业没有配置用户!');
		}
		$gateway = $entApp->gateway;

		$params = [
			"user_openid" => $user->openid,
			"ent_openid" => $ent->openid,
			"app_openid" => $app->openid,
			'ent_app_token' => $entApp->token
		];
		$token = false;
		if (empty($gateway)) {
			//没有网关时，用本地服务
			$token = app('Gmf\Sys\Bp\AppToken')->issueToken($params);
		} else {
			//远程服务授权
			$client = new GuzzleHttp\Client(['base_uri' => $gateway]);
			$res = $client->post('api/sys/apps/token', ['json' => $params]);
			$body = (String)$res->getBody();
			if ($body) {
				$body = json_decode($body);
				$token = $body && $body->data ? $body->data : false;
			}
		}
		$result = [
			'host' => $gateway ? : $request->getSchemeAndHttpHost(),
			'ent' => $ent->openid,
			'token' => $token
		];
		return $this->toJson($result);
	}
}
