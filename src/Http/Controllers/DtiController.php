<?php
namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Models;
use Illuminate\Http\Request;

class DtiController extends Controller {
	public function index(Request $request) {
		$query = Models\Dti::where('ent_id', $request->oauth_ent_id);

		$data = $query->get();

		return $this->toJson($data);
	}
	public function show(Request $request, string $id) {
		$query = Models\Dti::where('ent_id', $request->oauth_ent_id);
		$data = $query->where('id', $id)->orWhere('code', $id)->first();
		return $this->toJson($data);
	}
}
