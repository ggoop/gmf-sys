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
		$query = RoleEntity::with('role', 'entity','field')->where('ent_id', GAuth::entId());
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
		$lines = $request->input('datas');
		$datas=[];
		$deleteIds=[];

		foreach ($lines as $key => $value) {
			$value['ent_id']= GAuth::entId();
			if (!empty($value['sys_state']) && $value['sys_state'] == 'c') {
				$datas[]=$value;
				continue;
			}
			if (!empty($value['sys_state']) && $value['sys_state'] == 'u' && $value['id']) {
				$datas[]=$value;
			}
			if (!empty($value['sys_state']) && $value['sys_state'] == 'd' && !empty($value['id'])) {
				$deleteIds[]=$value['id'];
				continue;
			}
		}
		if(count($deleteIds)){
			RoleEntity::destroy($deleteIds);
		}
		if(count($datas)){
			RoleEntity::BatchImport($datas);
		}
		return $this->toJson(true);
	}
}
