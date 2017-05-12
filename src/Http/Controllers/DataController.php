<?php

namespace Gmf\Sys\Http\Controllers;
use Gmf\Sys\Uuid;
use Illuminate\Http\Request;

class DataController extends Controller {
	public function index(Request $request) {

		$datas = [];
		for ($i = 0; $i < 20; $i++) {
			$datas[] = Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
		}
		return $this->toJson($datas);
	}
	public function show(Request $request) {
		$datas = Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
		return $this->toJson($datas);
	}
}
