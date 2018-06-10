<?php
namespace Gmf\Sys\Http\Controllers\App;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GatewayController extends Controller {
	public function gateway(Request $request) {

		$data = ['aa' => true];

		return $this->toJson($data);
	}
}
