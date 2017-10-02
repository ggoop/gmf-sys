<?php
namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Models;
use Illuminate\Http\Request;

class ComponentController extends Controller {
	public function index(Request $request) {
		$data = false;
		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$id = kebab_case($id);
		$id = str_replace('-', '.', $id);

		$query = Models\Component::where('code', '!=', '');

		$data = $query->where('id', $id)->orWhere('code', $id)->first();
		return $this->toJson($data);
	}
}
