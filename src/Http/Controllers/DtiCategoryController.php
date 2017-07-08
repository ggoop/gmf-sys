<?php
namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Validator;

class DtiCategoryController extends Controller {
	public function index(Request $request) {
		$query = Models\DtiCategory::where('ent_id', $request->oauth_ent_id);
		if ($request->has('is_revoked')) {
			$query->where('is_revoked', $request->is_revoked);
		}
		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\DtiCategory::where('ent_id', $request->oauth_ent_id);
		$data = $query->where('id', $id)->first();
		return $this->toJson($data);
	}
	public function store(Request $request) {
		$input = $request->all();
		$input = array_only($request->all(), ['id', 'code', 'name', 'host', 'is_revoked']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$entId = $request->oauth_ent_id;
		$data = Models\DtiCategory::updateOrCreate(['ent_id' => $entId, 'code' => $input['code']], $input);
		return $this->show($request, $data->id);
	}
}
