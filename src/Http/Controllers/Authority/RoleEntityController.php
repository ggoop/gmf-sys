<?php
namespace Gmf\Sys\Http\Controllers\Authority;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Models\Authority\Role;
use Gmf\Sys\Models\Authority\RoleEntity;
use Gmf\Sys\Models\Entity;
use Illuminate\Http\Request;
use Validator;

class RoleEntityController extends Controller {
	public function show(Request $request, string $id) {
		$query = RoleEntity::where('ent_id', $request->oauth_ent_id);
		$data = $query->where('role_id', $id)->get();
		return $this->toJson($data);
	}
	public function store(Request $request) {
		$input = array_only($request->all(), ['is_revoked']);
		$input = InputHelper::fillEntity($input, $request, ['role', 'entity']);
		$validator = Validator::make($input, [
			'role_id' => 'required',
			'entity_id' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$entId = $request->oauth_ent_id;
		$data['ent_id'] = $entId;
		$data = RoleEntity::create($input);
		return $this->show($request, $data->id);
	}
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		RoleEntity::destroy($ids);
		return $this->toJson(true);
	}
	public function batchStore(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'datas' => 'required|array|min:1',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$entId = $request->oauth_ent_id;
		$datas = $request->input('datas');
		foreach ($datas as $k => $v) {
			$data = array_only($v, ['is_revoked']);
			$data = InputHelper::fillEntity($data, $v,
				[
					'role' => ['type' => Role::class, 'matchs' => ['code', 'ent_id' => '${ent_id}']],
					'entity' => ['type' => Entity::class, 'matchs' => ['code']],
				],
				['ent_id' => $entId]
			);
			if (!empty($data['role_id']) && !empty($data['entity_id'])) {
				RoleEntity::updateOrCreate(['ent_id' => $entId, 'role_id' => $data['role_id'], 'entity_id' => $data['entity_id']], $data);
			}
		}
		return $this->toJson(true);
	}
}
