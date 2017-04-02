<?php

namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Validator;

class OrgTeamController extends Controller {
	public function index(Request $request) {
		$query = Models\Team::with('org', 'dept', 'work');

		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\Team::with('org', 'dept', 'work');
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
		$oid = $request->input('org.id');
		if ($oid) {
			$input['org_id'] = $oid;
		}
		$oid = $request->input('dept.id');
		if ($oid) {
			$input['dept_id'] = $oid;
		}
		$oid = $request->input('work.id');
		if ($oid) {
			$input['work_id'] = $oid;
		}
		$data = Models\Team::create($input);

		return $this->show($request, $data->id);
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
		$oid = $request->input('org.id');
		if ($oid) {
			$input['org_id'] = $oid;
		}
		$oid = $request->input('dept.id');
		if ($oid) {
			$input['dept_id'] = $oid;
		}
		$oid = $request->input('work.id');
		if ($oid) {
			$input['work_id'] = $oid;
		}
		if (!Models\Team::where('id', $id)->update($input)) {
			return $this->toError('没有更新任何数据 !');
		}
		return $this->show($request, $id);
	}
	/**
	 * DELETE
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);

		Models\Team::destroy($ids);
		return $this->toJson(true);
	}
}
