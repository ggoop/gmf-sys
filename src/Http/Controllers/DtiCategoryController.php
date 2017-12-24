<?php
namespace Gmf\Sys\Http\Controllers;

use GAuth;
use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Validator;

class DtiCategoryController extends Controller {
	public function index(Request $request) {
		$query = Models\DtiCategory::with('params')->where('ent_id', GAuth::entId());
		if ($request->has('is_revoked')) {
			$query->where('is_revoked', $request->is_revoked);
		}
		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\DtiCategory::with('params')->where('ent_id', GAuth::entId());
		$data = $query->where('id', $id)->first();
		return $this->toJson($data);
	}
	public function store(Request $request) {
		$input = array_only($request->all(), ['id', 'code', 'name', 'host', 'is_revoked']);
		$validator = Validator::make($input, [
			'code' => [
				'required',
			],
			'name' => [
				'required',
			],
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$entId = GAuth::entId();
		$data = Models\DtiCategory::updateOrCreate(['ent_id' => $entId, 'code' => $input['code']], $input);
		return $this->show($request, $data->id);
	}
	/**
	 * PUT/PATCH
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request, $id) {
		$input = $request->only(['code', 'name', 'host', 'is_revoked']);
		$validator = Validator::make($input, [
			'code' => [
				'required',
			],
			'name' => [
				'required',
			],
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\DtiCategory::where('id', $id)->update($input);
		return $this->show($request, $id);
	}
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		Models\DtiCategory::destroy($ids);
		return $this->toJson(true);
	}
}
