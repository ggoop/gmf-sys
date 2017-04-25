<?php

namespace Gmf\Sys\Http\Controllers;
use Gmf\Sys\Libs\TreeBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller {
	public function index(Request $request) {
		$query = Models\Menu::select('id', 'code', 'name', 'memo', 'scope_enum');
		$data = $query->get();
		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\Menu::select('id', 'code', 'name', 'memo', 'scope_enum');
		$data = $query->where('id', $id)->orWhere('code', $id)->first();
		return $this->toJson($data);
	}

	/**
	 * POST
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
	public function store(Request $request) {
		$input = $request->all();
		$validator = Validator::make($input, [
			'code' => 'required|max:255|min:3',
			'name' => 'required',
			'scope_enum' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = Models\Menu::create($input);
		return $this->toJson($data);
	}
	/**
	 * PUT/PATCH
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function update(Request $request, $id) {
		$input = $request->intersect(['code', 'name']);
		$validator = Validator::make($input, [
			'code' => 'max:255|min:3',
			'name' => 'min:1|max:255',
			'scope_enum' => 'min:1|max:255',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$data = [];
		if (Models\Menu::where('id', $id)->update($input)) {
			$data = Models\Menu::find($id);
		} else {
			return $this->toError('没有更新任何数据 !');
		}
		return $this->toJson($data);
	}
	/**
	 * DELETE
	 * @param  Request $request [description]
	 * @param  [type]  $id      [description]
	 * @return [type]           [description]
	 */
	public function destroy(Request $request, $id) {
		$ids = explode(",", $id);
		Models\Menu::destroy($ids);
		return $this->toJson(true);
	}
	public function all(Request $request, $menuID = '') {
		$query = DB::table('gmf_sys_menus as m')
			->join('gmf_sys_menu_relations as r', 'm.id', '=', 'r.menu_id')
			->select('r.root_id', 'r.parent_id', 'm.id', 'm.code', 'm.name', 'm.uri', 'r.sequence')
			->addSelect('m.icon')
			->addSelect('m.style')
			->orderBy('r.sequence')
			->orderBy('r.root_id')
			->orderBy('r.parent_id')
			->orderBy('m.code');
		if ($request->input('tag')) {
			$query->where('m.tag', $request->input('tag'));
		}
		$result = $query->get();
		$tree = TreeBuilder::create($result);
		return $this->toJson($tree);
	}
}
