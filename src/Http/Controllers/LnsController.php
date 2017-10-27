<?php
namespace Gmf\Sys\Http\Controllers;

use Gmf\Sys\Libs\Common;
use Gmf\Sys\Models;
use Gmf\Sys\Uuid;
use Illuminate\Http\Request;
use Validator;

class LnsController extends Controller {
	public function issueRequest(Request $request) {
		//cpu+code+item[code,number];
		$input = array_only($request->all(), ['items', 'fm_date', 'to_date']);
		$validator = Validator::make($input, [
			'fm_date' => 'required',
			'to_date' => 'required',
			'items' => 'required',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		exec("wmic CPU get ProcessorID", $result);
		$input['code'] = $result[1];
		$input['id'] = Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
		$input['serial_number'] = Common::EncryptDES($input['code'], 'gmf');
		$rc = array_only($input, ['code', 'serial_number', 'fm_date', 'to_date', 'id']);

		$rcls = [];
		foreach ($input['items'] as $key => $value) {
			$rcls[] = ['code' => $value['code'], 'number' => $value['code']];
		}
		$rcls = array_values(array_sort($rcls, function ($value) {
			return $value['code'];
		}));
		$rc['items'] = $rcls;

		ksort($rc);

		$input['request_code'] = Common::EncryptDES(json_encode($rc), 'gmf');

		$data = Models\Lns::create($input);
		return $this->toJson($data->request_code);
	}
	public function issueAnswer(Request $request) {
		$input = array_only($request->all(), ['code']);
		$validator = Validator::make($input, [
			'code' => 'required',
		]);
		$request_code = $input['code'];
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}
		$codeObj = json_decode(Common::DecryptDES($request_code, 'gmf'));
		if (empty($codeObj)
			|| empty($codeObj->id)
			|| empty($codeObj->code)
			|| empty($codeObj->serial_number)) {
			throw new \Exception("code is bad", 1);
		}
		if ($codeObj->serial_number !== Common::EncryptDES($codeObj->code, 'gmf')) {
			throw new \Exception("code is altered", 1);
		}
		$answerCode = Common::EncryptDES($request_code, $request_code);
		return $this->toJson($answerCode);
	}
	public function storeRegist(Request $request) {
	}
}
