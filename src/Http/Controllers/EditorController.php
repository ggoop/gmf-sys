<?php

namespace Gmf\Sys\Http\Controllers;
use Gmf\Sys\Models\Editor;
use Illuminate\Http\Request;

class EditorController extends Controller {
	public function templates(Request $request) {
		$datas = [];
		$items = Editor\Template::select('id', 'title', 'memo as description', 'content')->where('is_revoked', '0')->get();

		return $items;
	}
}
