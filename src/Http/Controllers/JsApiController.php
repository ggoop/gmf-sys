<?php
namespace Gmf\Sys\Http\Controllers;

use Illuminate\Http\Request;

class JsApiController extends Controller {
	public function config(Request $request) {

		$data = ['aa' => true];

		return $this->toJson($data);
	}
}
