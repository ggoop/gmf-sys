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
