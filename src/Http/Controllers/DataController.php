<?php

namespace Gmf\Sys\Http\Controllers;
use Gmf\Sys\Query\EntityQuery;
use Gmf\Sys\Query\Filter;
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
	public function test(Request $request) {

		$w = Filter::create();
		$w->parse($request->input('wheres'));
		$eq = EntityQuery::create('suite.amiba.group');

		$eq->addWheres($w->getItems());
		$sql = $eq->toSql();
		$bs = $eq->getBindings();
		var_dump($bs);
		return $this->toJson($sql);
	}
}
