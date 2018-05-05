<?php
namespace Gmf\Sys\Http\Controllers;

use GAuth;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Validator;

class DtiParamController extends Controller {
	public function index(Request $request) {
		$query = Models\DtiParam::with('category', 'dti')->where('ent_id', GAuth::entId());
		if ($request->has('revoked')) {
			$query->where('revoked', $request->revoked);
		}
		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\DtiParam::with('category', 'dti')->where('ent_id', GAuth::entId());
		$data = $query->where('id', $id)->first();
		return $this->toJson($data);
	}
	public function store(Request $request) {
		$input = array_only($request->all(), ['id', 'code', 'name', 'category_id', 'dti_id', 'type_enum', 'value', 'revoked']);
		GAuth::check('user');
		$input = InputHelper::fillEntity($input, $request, ['category', 'dti']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$input['ent_id'] = GAuth::entId();

		$data = Models\DtiParam::create($input);
		return $this->show($request, $data->id);
	}
	public function update(Request $request, $id) {
		$input = array_only($request->all(), ['id', 'code', 'name', 'category_id', 'dti_id', 'type_enum', 'value', 'revoked']);
		GAuth::check('user');
		$input = InputHelper::fillEntity($input, $request, ['category', 'dti']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\DtiParam::where('id', $id)->update($input);
		return $this->show($request, $id);
	}
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		GAuth::check('user');
		Models\DtiParam::destroy($ids);
		return $this->toJson(true);
	}
}
