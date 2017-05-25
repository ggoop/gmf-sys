<?php

namespace Gmf\Sys\Http\Controllers;
use Gmf\Sys\Libs\TreeBuilder;
use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller {
	public function index(Request $request) {
		$query = Models\Menu::where('id', '!=', '');
		$data = $query->get();
		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\Menu::where('id', '!=', '');
		$data = $query->where('id', $id)->orWhere('code', $id)->first();
		return $this->toJson($data);
	}
	/**
	 * 获取菜单路径,上级->下级
	 * @param  Request $request [description]
	 * @param  string  $menuID  [description]
	 * @return [type]           [description]
	 */
	public function getPath(Request $request, $menuID = '') {
		$datas = [];
		$query = Models\Menu::where(function ($query) use ($menuID) {
			$query->where('id', $menuID)->orWhere('code', $menuID)->orWhere('uri', $menuID);
		});
		if ($request->input('tag')) {
			$query->where('tag', $request->input('tag'));
		}
		$curr = $query->first();
		if ($curr) {
			$datas[] = ['id' => $curr->id, 'name' => $curr->name, 'uri' => $curr->uri];
		}
		while ($curr && $curr->id != $curr->parent_id && $curr->parent_id) {
			$curr = Models\Menu::where('id', $curr->parent_id)->first();
			if ($curr) {
				$datas[] = ['id' => $curr->id, 'name' => $curr->name, 'uri' => $curr->uri];
			}
		}
		//倒转集合内项目的顺序
		$result = [];
		for ($i = count($datas) - 1; $i >= 0; $i--) {
			$result[] = $datas[$i];
		}
		return $this->toJson($result);
	}
	public function all(Request $request, $menuID = '') {
		$query = DB::table('gmf_sys_menus as m')
			->select('m.root_id', 'm.parent_id', 'm.id', 'm.code', 'm.name', 'm.uri', 'm.sequence', 'm.memo')
			->addSelect('m.icon')
			->addSelect('m.style')
			->addSelect('m.params')
			->orderBy('m.sequence')
			->orderBy('m.root_id')
			->orderBy('m.parent_id')
			->orderBy('m.code');
		if ($request->input('tag')) {
			$query->where('m.tag', $request->input('tag'));
		}
		$result = $query->get();
		$tree = TreeBuilder::create($result);
		return $this->toJson($tree);
	}
}
