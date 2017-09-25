<?php
namespace Gmf\Sys\Http\Controllers;

use Artisan;
use Auth;
use DB;
use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Validator;

class EntController extends Controller {
	public function index(Request $request) {
		$userID = Auth::id();
		$query = DB::table('gmf_sys_ents as l')->join('gmf_sys_ent_users as u', 'l.id', '=', 'u.ent_id');
		$query->addSelect('l.id', 'l.name', 'l.avatar', 'l.dc_host', 'u.is_default', 'u.type_enum as type');
		$query->where('u.user_id', $userID);
		$query->orderBy('u.is_default', 'desc')->orderBy('l.name');

		$datas = $query->get();
		return $this->toJson($datas);
	}
	public function show(Request $request, string $id) {
		$query = Models\Ent::where('id', $id)->orWhere('code', $id);
		$data = $query->first();
		return $this->toJson($data);
	}
	public function store(Request $request) {
		$input = array_only($request->all(), ['code', 'name', 'memo', 'short_name', 'avatar', 'dc_host', 'industry', 'area']);
		$validator = Validator::make($input, [
			'code' => [
				'required',
			],
			'name' => [
				'required',
			],
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = Models\Ent::create($input);

		Models\Ent::addUser($data->id, Auth::id(), 'create');
		return $this->show($request, $data->id);
	}
	/**
	 * PUT/PATCH
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request, $id) {
		$input = $request->only(['code', 'name', 'memo', 'short_name', 'avatar', 'dc_host', 'industry', 'area']);
		$validator = Validator::make($input, [
			'code' => [
				'required',
			],
			'name' => [
				'required',
			],
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		Models\Ent::where('id', $id)->update($input);
		return $this->show($request, $id);
	}
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		Models\Ent::destroy($ids);
		return $this->toJson(true);
	}
	public function getMyEnts(Request $request) {
		$userID = Auth::id();
		$query = DB::table('gmf_sys_ents as l')->join('gmf_sys_ent_users as u', 'l.id', '=', 'u.ent_id');
		$query->addSelect('l.id', 'l.name', 'l.avatar', 'l.dc_host', 'u.is_default', 'u.type_enum as type');
		$query->where('u.user_id', $userID);
		$query->orderBy('u.is_default', 'desc')->orderBy('l.name');

		$datas = $query->get();
		return $this->toJson($datas);
	}
	public function seedDatas(Request $request, $id) {
		$datas = [];
		if ($id) {
			$datas[] = Artisan::call('gmf:seed', [
				'--tag' => 'pre', '--ent' => $id,
			]);
			$datas[] = Artisan::call('gmf:seed', [
				'--ent' => $id,
			]);
			$datas[] = Artisan::call('gmf:seed', [
				'--tag' => 'post', '--ent' => $id,
			]);
		}
		return $this->toJson($datas);
	}
}
