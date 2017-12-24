<?php

namespace Gmf\Sys\Http\Controllers;
use DB;
use GAuth;
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
		return GAuth::entId();

		$datas = Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
		return $this->toJson($datas);
	}
	public function issueUid(Request $request) {
		$num = $request->input('num', 1);
		$num = intval($num);
		$num = $num > 0 ? $num : 1;
		DB::statement('SET @num =' . $num . ';');
		DB::statement("CALL sp_gmf_sys_uid(?,@num);", [$request->input('node')]);
		$datas = DB::select('select @num as sn');
		return $this->toJson($datas && count($datas) ? $datas[0]->sn : false);
	}
}
