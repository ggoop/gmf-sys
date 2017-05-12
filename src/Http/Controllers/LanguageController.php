<?php

namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Models;
use Illuminate\Http\Request;

class LanguageController extends Controller {
	public function index(Request $request) {
		$query = Models\Language::select('id', 'code', 'name', 'memo');

		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\Language::select('id', 'code', 'name', 'memo');
		$data = $query->where('id', $id)->orWhere('code', $id)->first();
		return $this->toJson($data);
	}
}
