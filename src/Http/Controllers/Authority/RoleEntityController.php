<?php
namespace Gmf\Sys\Http\Controllers\Authority;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Models\Authority\Role;
use Gmf\Sys\Models\Authority\RoleEntity;
use Gmf\Sys\Models\Entity;
use Illuminate\Http\Request;
use Validator;
use GAuth;
class RoleEntityController extends Controller {
	public function index(Request $request) {
		$query = RoleEntity::with('role', 'entity')->where('ent_id', GAuth::entId());
		$matchs = array_only($request->all(), ['role_id', 'entity_id', 'operation_enum']);
		if ($matchs && count($matchs)) {
			$query->where($matchs);
		}
		$data = $query->get();
		return $this->toJson($data);
	}
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		RoleEntity::destroy($ids);
		return $this->toJson(true);
	}
	public function store(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'datas' => 'array',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$entId = GAuth::entId();
		$lines = $request->input('datas');

		$fillable = ['operation_enum', 'filter'];
		$entityable = [
			'role' => ['type' => Role::class, 'matchs' => ['code', 'ent_id' => '${ent_id}']],
			'entity' => ['type' => Entity::class, 'matchs' => ['code']],
		];

		foreach ($lines as $key => $value) {
			if (!empty($value['sys_state']) && $value['sys_state'] == 'c') {
				$data = array_only($value, $fillable);
				$data = InputHelper::fillEntity($data, $value, $entityable, ['ent_id' => $entId]);

				$data['ent_id'] = $entId;
				RoleEntity::create($data);
				continue;
			}
			if (!empty($value['sys_state']) && $value['sys_state'] == 'u' && $value['id']) {
				$data = array_only($value, $fillable);
				$data = InputHelper::fillEntity($data, $value, $entityable, ['ent_id' => $entId]);
				RoleEntity::where('id', $value['id'])->update($data);
			}
			if (!empty($value['sys_state']) && $value['sys_state'] == 'd' && !empty($value['id'])) {
				RoleEntity::destroy($value['id']);
				continue;
			}
		}
		return $this->toJson(true);
	}
}
