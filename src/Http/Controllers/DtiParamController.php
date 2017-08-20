<?php
namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Validator;

class DtiParamController extends Controller {
	public function index(Request $request) {
		$query = Models\DtiParam::where('ent_id', $request->oauth_ent_id);
		if ($request->has('is_revoked')) {
			$query->where('is_revoked', $request->is_revoked);
		}
		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\DtiParam::where('ent_id', $request->oauth_ent_id);
		$data = $query->where('id', $id)->first();
		return $this->toJson($data);
	}
	public function store(Request $request) {
		$input = $request->all();
		$input = array_only($request->all(), ['id', 'code', 'name', 'category_id', 'dti_id', 'type_enum', 'value', 'is_revoked']);

		$input = InputHelper::fillEntity($input, $request, ['category', 'dti']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
			'category_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$entId = $request->oauth_ent_id;
		if (!empty($input['id'])) {
			$find['id'] = $input['id'];
		} else {
			$find = ['ent_id' => $entId, 'code' => $input['code'], 'category_id' => $input['category_id']];
			if (!empty($input['dti_id'])) {
				$find['dti_id'] = $input['dti_id'];
			} else {
				$find['dti_id'] = null;
			}
		}

		$data = Models\DtiParam::updateOrCreate($find, $input);
		return $this->show($request, $data->id);
	}
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		Models\DtiParam::destroy($ids);
		return $this->toJson(true);
	}
}
