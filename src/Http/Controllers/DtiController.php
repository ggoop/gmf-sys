<?php
namespace Gmf\Sys\Http\Controllers;

use GAuth;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Validator;

class DtiController extends Controller {
	public function index(Request $request) {
		$size = $request->input('size', 10);
		$query = Models\Dti::with('category', 'local');
		$query->where('ent_id', GAuth::entId());
		$data = $query->paginate($size);
		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\Dti::with('category', 'local')->where('ent_id', GAuth::entId());
		$data = $query->where('id', $id)->orWhere('code', $id)->first();
		return $this->toJson($data);
	}
	public function store(Request $request) {
		GAuth::check('user');
		$input = array_only($request->all(), ['code', 'name', 'host', 'path', 'method_enum',
			'header', 'body', 'query', 'memo']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$input = InputHelper::fillEntity($input, $request, ['category', 'local']);

		$input['ent_id'] = GAuth::entId();
		$data = Models\Dti::create($input);
		return $this->show($request, $data->id);
	}
	/**
	 * PUT/PATCH
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request, $id) {
		$input = array_only($request->all(), ['code', 'name', 'host', 'path', 'method_enum',
			'header', 'body', 'query', 'memo']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		GAuth::check('user');
		$input = InputHelper::fillEntity($input, $request, ['category', 'local']);
		$entId = GAuth::entId();
		$data = Models\Dti::updateOrCreate(['id' => $id], $input);
		return $this->show($request, $data->id);
	}
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		GAuth::check('user');
		Models\Dti::destroy($ids);
		return $this->toJson(true);
	}
}
