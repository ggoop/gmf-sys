<?php
namespace Gmf\Sys\Http\Controllers\App;

use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class ConfigController extends Controller
{
    /**
     * 应用服务配置，授权
     * [{appId}]
     */
    public function config(Request $request)
    {
        $input = $request->all();
        Validator::make($input, [
            'appId' => 'required',
        ])->validate();
        $token = app('Gmf\Sys\Bp\AppConfig')->config([
            'appId' => $input['appId'],
            'userId' => GAuth::id(),
            'entId' => GAuth::entId(),
        ]);
        if (empty($token['host'])) {
            $token['host'] = $request->getSchemeAndHttpHost();
        }
        return $this->toJson($token);
    }
}
