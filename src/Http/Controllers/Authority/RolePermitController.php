<?php
namespace Gmf\Sys\Http\Controllers\Authority;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Models\Authority\Permit;
use Gmf\Sys\Models\Authority\Role;
use Gmf\Sys\Models\Authority\RolePermit;
use Illuminate\Http\Request;
use Validator;

class RolePermitController extends Controller {
	public function index(Request $request) {
		$query = RolePermit::with('role', 'permit')->where('ent_id', $request->oauth_ent_id);
		$matchs = array_only($request->all(), ['role_id', 'permit_id', 'opinion_enum']);
		if ($matchs && count($matchs)) {
			$query->where($matchs);
		}
		$data = $query->get();
		return $this->toJson($data);
	}
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		RolePermit::destroy($ids);
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
		$entId = $request->oauth_ent_id;
		$lines = $request->input('datas');

		$fillable = ['is_revoked', 'opinion_enum'];
		$entityable = [
			'role' => ['type' => Role::class, 'matchs' => ['code', 'ent_id' => '${ent_id}']],
			'permit' => ['type' => Permit::class, 'matchs' => ['code']],
		];

		foreach ($lines as $key => $value) {
			if (!empty($value['sys_state']) && $value['sys_state'] == 'c') {
				$data = array_only($value, $fillable);
				$data = InputHelper::fillEntity($data, $value, $entityable, ['ent_id' => $entId]);

				$data['ent_id'] = $entId;
				RolePermit::create($data);
				continue;
			}
			if (!empty($value['sys_state']) && $value['sys_state'] == 'u' && $value['id']) {
				$data = array_only($value, $fillable);
				$data = InputHelper::fillEntity($data, $value, $entityable, ['ent_id' => $entId]);
				RolePermit::where('id', $value['id'])->update($data);
			}
			if (!empty($value['sys_state']) && $value['sys_state'] == 'd' && !empty($value['id'])) {
				RolePermit::destroy($value['id']);
				continue;
			}
		}
		return $this->toJson(true);
	}
}
