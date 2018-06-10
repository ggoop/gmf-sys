<?php
namespace Gmf\Sys\Http\Controllers\App;
use Gmf\Sys\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller {
	public function register(Request $request) {

		$data = ['aa' => true];

		return $this->toJson($data);
	}
}
