<?php

namespace Gmf\Sys\Http\Controllers;
use DB;
use GAuth;
use Gmf\Sys\Libs\TreeBuilder;
use Gmf\Sys\Models;
use Illuminate\Http\Request;

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
		$datas = DB::select('call sp_gmf_sys_menus(?,?,?)', [
			GAuth::entId(),
			GAuth::userId(),
			$request->input('tag')]);

		$tree = TreeBuilder::create($datas);
		return $this->toJson($tree);
	}
}
