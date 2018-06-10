<?php
namespace Gmf\Sys\Http\Controllers\App;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DiscoverController extends Controller {
	/**
	 * 通过服务发现，查找服务地址
	 * [{ent,app,name,url,method,scheme,host,port,path}]
	 */
	public function discover(Request $request) {
		
		$data = ['aa' => true];

		return $this->toJson($data);
	}
}
