<?php
namespace Gmf\Sys\Http\Controllers\Authority;
use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Models\Authority\Role;
use Illuminate\Http\Request;
use Validator;

class RoleController extends Controller {
	public function index(Request $request) {
		$query = Role::where('ent_id', GAuth::entId());
		$matchs = array_only($request->all(), ['code', 'name']);
		if ($matchs && count($matchs)) {
			$query->where($matchs);
		}
		return $this->toJson($query->paginate($request->input('size', 10)));
	}
	public function show(Request $request, string $id) {
		$query = Role::where('ent_id', GAuth::entId());
		$data = $query->where('id', $id)->orWhere('code', $id)->first();
		return $this->toJson($data);
	}
	public function store(Request $request) {
		$input = array_only($request->all(), ['code', 'name', 'memo', 'type_enum']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
			'type_enum' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$entId = GAuth::entId();
		$input['ent_id'] = $entId;
		$data = Role::create($input);
		return $this->show($request, $data->id);
	}
	/**
	 * PUT/PATCH
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request, $id) {
		$input = array_only($request->all(), ['code', 'name', 'memo', 'type_enum']);
		$validator = Validator::make($input, [
			'code' => 'required',
			'name' => 'required',
			'type_enum' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = Role::updateOrCreate(['id' => $id], $input);
		return $this->show($request, $data->id);
	}
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		Role::destroy($ids);
		return $this->toJson(true);
	}
}
