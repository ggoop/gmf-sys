<?php

namespace Gmf\Sys\Http\Controllers;
use Illuminate\Http\Request;

class UserController extends Controller {
	public function index(Request $request) {
		$query = config('gmf.user.model')::where('id', '!=', '');
		$data = $query->get();
		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = config('gmf.user.model')::where('id', '!=', '');
		$data = $query->where('id', $id)->first();
		return $this->toJson($data);
	}
}
