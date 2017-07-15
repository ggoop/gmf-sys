<?php

namespace Gmf\Sys\Http\Controllers;
use Gmf\Sys\Models;
use Gmf\Sys\Uuid;
use Illuminate\Http\Request;

class DataController extends Controller {
	public function index(Request $request) {
		//throw new \Exception("接口未实现");
		$datas = [];
		for ($i = 0; $i < 20; $i++) {
			$datas[] = Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
		}
		return $this->toJson($datas);
	}
	public function show(Request $request) {
		$query = Models\Menu::with('menus')
			->where('tag', '8ce1c840288211e78c76c5590f4f3b16')
			->where('id', '129412d0288511e7bc87436a79f6e829');
		$rtn = $query->get();
		return $this->toJson($rtn);
		$datas = Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
		return $this->toJson($datas);
	}
}
