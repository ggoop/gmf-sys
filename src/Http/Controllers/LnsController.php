<?php
namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Lns\Createor;
use Illuminate\Http\Request;
use Validator;

class LnsController extends Controller {
	public function issueRequest(Request $request) {
		$input = array_only($request->all(), ['content', 'fm_date', 'to_date']);
		$validator = Validator::make($input, [
			'content' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		return $this->toJson(Createor::issueRequestCode($input));
	}
	public function issueAnswer(Request $request) {
		$input = array_only($request->all(), ['code']);
		$validator = Validator::make($input, [
			'code' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		return $this->toJson(Createor::issueAnswer($input['code']));
	}
	public function storeRegist(Request $request) {
		$input = array_only($request->all(), ['req_code', 'ans_code']);
		$validator = Validator::make($input, [
			'req_code' => 'required',
			'ans_code' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		return $this->toJson(Createor::issueRegist($input['req_code'], $input['ans_code'], $request));
	}
	public function getWebRegist(Request $request) {
		return view('gmf::lns.regist', ['ent_id' => config('gmf.ent.id')]);
	}
	public function storeWebRegist(Request $request) {
		$input = array_only($request->all(), ['ent_id', 'content']);
		$validator = Validator::make($input, [
			'ent_id' => 'required',
			'content' => 'required',
		]);
		if ($validator->fails()) {
			$this->throwValidationException(
				$request, $validator
			);
		}
		$req_code = Createor::issueRequestCode(['content' => $input['content']]);

		$ans_code = Createor::issueAnswer($req_code);
		Createor::issueRegist($req_code, $ans_code, $request);

		return view('gmf::lns.regist', ['ent_id' => config('gmf.ent.id')]);
	}
}
