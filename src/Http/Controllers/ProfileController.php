<?php
namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Validator;

class ProfileController extends Controller {
	public function index(Request $request) {
		$query = Models\Profile::select('id', 'code', 'name', 'memo', 'scope_enum');

		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\Profile::select('id', 'code', 'name', 'memo', 'scope_enum');
		$data = $query->where('id', $id)->orWhere('code', $id)->first();
		if ($data) {
			$data->value = Models\ProfileValue::select('value')->where('profile_id', $data->id)->first();
		}
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
			'scope_enum' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = Models\Profile::create($input);
		return $this->toJson($data);
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
			$v = array_only($v, ['code', 'name', 'memo', 'dValue', 'scope_enum']);
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
			'scope_enum' => 'min:1|max:255',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = [];
		if (Models\Profile::where('id', $id)->update($input)) {
			$data = Models\Profile::find($id);
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
		Models\Profile::destroy($ids);
		return $this->toJson(true);
	}
}
