<?php
namespace Gmf\Sys\Http\Controllers\Authority;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Models\Authority\Role;
use Gmf\Sys\Models\Authority\RoleMenu;
use Gmf\Sys\Models\Menu;
use Illuminate\Http\Request;
use Validator;
use GAuth;
class RoleMenuController extends Controller {
	public function index(Request $request) {
		$query = RoleMenu::with('role', 'menu')->where('ent_id', GAuth::entId());
		$matchs = array_only($request->all(), ['role_id', 'menu_id', 'opinion_enum']);
		if ($matchs && count($matchs)) {
			$query->where($matchs);
		}
		$data = $query->get();
		return $this->toJson($data);
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

		$fillable = ['revoked', 'opinion_enum'];
		$entityable = [
			'role' => ['type' => Role::class, 'matchs' => ['code', 'ent_id' => '${ent_id}']],
			'menu' => ['type' => Menu::class, 'matchs' => ['code']],
		];

		foreach ($lines as $key => $value) {
			if (!empty($value['sys_state']) && $value['sys_state'] == 'c') {
				$data = array_only($value, $fillable);
				$data = InputHelper::fillEntity($data, $value, $entityable, ['ent_id' => $entId]);

				$data['ent_id'] = $entId;
				RoleMenu::create($data);
				continue;
			}
			if (!empty($value['sys_state']) && $value['sys_state'] == 'u' && $value['id']) {
				$data = array_only($value, $fillable);
				$data = InputHelper::fillEntity($data, $value, $entityable, ['ent_id' => $entId]);
				RoleMenu::where('id', $value['id'])->update($data);
			}
			if (!empty($value['sys_state']) && $value['sys_state'] == 'd' && !empty($value['id'])) {
				RoleMenu::destroy($value['id']);
				continue;
			}
		}
		return $this->toJson(true);
	}
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		RoleMenu::destroy($ids);
		return $this->toJson(true);
	}
}
