<?php

namespace Gmf\Sys\Http\Controllers;

use Auth;
use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Validator;

class OrgOrgController extends Controller {
	public function index(Request $request) {
		return Auth::user()->createToken('Token Name')->accessToken;
		$query = Models\Org::where('ent_id', 1);

		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		return $request->oauth_client_id . ':' . $request->oauth_user_id;
		$query = Models\Org::select('id', 'code', 'name', 'memo');
		$data = $query->where('id', $id)->orWhere('code', $id)->first();
		return $this->toJson($data);
	}

	/**
	 * POST
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function store(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'code' => 'required|max:255|min:3',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = Models\Org::create($input);
		return $this->show($request, $data->id);
	}
	public function batchStore(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'datas' => 'required|array|min:1',
			'datas.*.code' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$entId = $request->oauth_client_id;
		//增加
		$datas = $request->input('datas');

		foreach ($datas as $k => $v) {
			$v = array_only($v, ['code', 'name', 'memo', 'shortName']);
			Models\Profile::updateOrCreate(['ent_id' => $entId, 'code' => $v['code']], $v);
		}
		return $this->toJson(true);
	}
	/**
	 * PUT/PATCH
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request, $id) {
		$input = $request->intersect(['code', 'name']);
		$validator = Validator::make($input, [
			'code' => 'max:255|min:3',
			'name' => 'min:1|max:255',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = [];
		if (Models\Org::where('id', $id)->update($input)) {
			$data = Models\Org::find($id);
		} else {
			return $this->toError('没有更新任何数据 !');
		}
		return $this->toJson($data);
	}
	/**
	 * DELETE
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		Models\Org::destroy($ids);
		return $this->toJson(true);
	}
}
