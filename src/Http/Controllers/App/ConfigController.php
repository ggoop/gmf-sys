<?php
namespace Gmf\Sys\Http\Controllers\App;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GAuth;
class ConfigController extends Controller {
	/**
	 * 通过服务发现，查找服务地址
	 * [{host,app,ent,token}]
	 */
	public function config(Request $request) {		
		$data = [
			'host'=>'http://suite.test',
			'ent' => '01e853947bda16d0bffe63bae7ec2c3b',
			'token'=>[
				'access_token'=>'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjdjNDFkMzhhMTVjZDRiMGRmNWY0NzQ0N2RjZjAzMmRjYjc2MTBmNzJjNTk3ZDA0MzU2Mjc1MjMxNGU3NWJiZjlkYmI1ODk5ODUzZWU0MWVmIn0.eyJhdWQiOiIwMWU4NTM5NDcyMjQxNjUwYjM5MTY3ZmUzZjg0ODcyZSIsImp0aSI6IjdjNDFkMzhhMTVjZDRiMGRmNWY0NzQ0N2RjZjAzMmRjYjc2MTBmNzJjNTk3ZDA0MzU2Mjc1MjMxNGU3NWJiZjlkYmI1ODk5ODUzZWU0MWVmIiwiaWF0IjoxNTI4NTQ4MzUzLCJuYmYiOjE1Mjg1NDgzNTMsImV4cCI6MTU2MDA4NDM1Mywic3ViIjoiMDFlODZiZDJiNzljMTU4MGFkOGQzZjE4NjA5ZWQxM2MiLCJzY29wZXMiOltdfQ.bE36nFJfyJc-1qODpYAoJ9M5YFu-5mX-j5wpa7Uff9fOXvqh_Jrnx2goV49RtsrF7LSX1cxgIpnj4qL5qJal7LhafRwg0jtdZQp6sfzuwB_9yjspcRzwjt3i5Wg60W9PgEktJHm5oMFiERUVuaVE1rEFCDjyhyGMJ2RJ-OHLc-uodqkNsVbO3ZcxJEGgt0xtbwl-kAFVafPzw9scOpahMZoDCI4VoebtQwtFSLLMKjb-cKWvodjMOQcx19Qb3cNMen9i185A67SfK3Q4tn0Mo5ZOTi6aKUyX_yR4bmrELXEpmz35B4UaetiKOVt3DB12bHOoaO_5W_jDlvexqD44ayIiYK0HhI1LBV01d_7EekZCYqrb5cY4-f5KG3JhAlqIxrfPRvNnFws8DXz5s0HGlcjy4c7XRDABNzNmnUImX_xrxuoaUqT46lZznS7rykbzKZE1pEl-BVVDZ4Up4ipRW0KNtj1-esyuAxgs02hyiNJLM67MV5qlRlCM4Rp-ZCljpYYzZ5TU4MMGrcIzHibB9v4Jw6pFyXO7ieDPLkM2d2DEc5WRzqP0Gj7EDmQ3E3pusPtRWmoy0f8Lv3b4zQ2nNGZKhj6MTZud0LfdXJJ_vLO926JQZwLKucuxfFIPxaX1CXSzJZvHYAwZEODMFTPfnlN3MPWxw8wj-rK1BF2CKUU'
			]
		];
		return $this->toJson($data);
	}
}
