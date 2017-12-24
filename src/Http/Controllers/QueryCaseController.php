<?php

namespace Gmf\Sys\Http\Controllers;

use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Gmf\Sys\Query\QueryCase;
use Illuminate\Http\Request;
use Validator;

class QueryCaseController extends Controller {

	public function show(Request $request, string $caseID) {
		$query = Models\QueryCase::where('id', $caseID);
		$data = $query->first();
		return $this->toJson($data);
	}
	public function store(Request $request) {
		$input = array_only($request->all(), ['id', 'query_id',
			'name', 'comment', 'data', 'size']);
		$validator = Validator::make($input, [
			'query_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		if (empty($input['size'])) {
			$input['size'] = 0;
		}

		$input['ent_id'] = GAuth::entId();
		$input['user_id'] = GAuth::userId();

		$content = new Builder;
		$content->wheres($request->input('wheres'));
		$content->orders($request->input('orders'));
		$content->fields($request->input('fields'));
		$input['data'] = json_encode($content);
		if (empty($input['id'])) {
			$data = Models\QueryCase::create($input);
		} else {
			$data = Models\QueryCase::updateOrCreate(['id' => $input['id']], $input);
		}
		return $this->toJson($data);
	}
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		Models\QueryCase::destroy($ids);
		return $this->toJson(true);
	}
}
