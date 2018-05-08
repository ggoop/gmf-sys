<?php

namespace Gmf\Sys\Lns;
use Carbon\Carbon;
use Closure;
use Gmf\Sys\Libs\Common;
use Gmf\Sys\Models;
use Uuid;
use Illuminate\Http\Request;
use GAuth;
class Createor {
	public static function issueRequestCode(array $input, Closure $callback = null) {
		//cpu+code+item[code,number];
		exec("wmic CPU get ProcessorID", $result);
		if (empty($input['fm_date'])) {
			$input['fm_date'] = Carbon::now()->toDateString();
		}
		if (empty($input['to_date'])) {
			$input['to_date'] = Carbon::now()->addYears(1)->toDateString();
		}
		$input['id'] = Uuid::generate();
		$input['serial'] = Common::EncryptDES($result[1], 'gmf');
		$rc = array_only($input, ['id', 'serial', 'fm_date', 'to_date', 'content']);
		$rc['cid'] = Common::EncryptDES($input['id'], 'gmf');

		$rcls = [];
		if (!empty($input['content'])) {
			$items = explode(',', $input['content']);
			foreach ($items as $key => $value) {
				$item = explode(':', $value);
				if ($item && count($item) == 2) {
					$rcls[] = ['code' => $item[0], 'number' => $item[1]];
				}
			}
			$rcls = array_values(array_sort($rcls, function ($value) {
				return $value['code'];
			}));
		}
		$rc['items'] = $rcls;

		ksort($rc);

		$input['code'] = Common::EncryptDES(json_encode($rc), 'gmf');

		$data = Models\LnsRequest::create($input);
		return $data->code;
	}
	public static function issueAnswer($reqCode, Closure $callback = null) {
		$codeObj = json_decode(Common::DecryptDES($reqCode, 'gmf'));

		if (empty($codeObj)
			|| empty($codeObj->id)
			|| empty($codeObj->cid)
			|| empty($codeObj->serial)) {
			throw new \Exception("code is bad", 1);
		}
		if ($codeObj->cid !== Common::EncryptDES($codeObj->id, 'gmf')) {
			throw new \Exception("code is altered", 1);
		}
		$codeObj->aid = Uuid::generate();

		$ansCode = Common::EncryptDES(json_encode($codeObj), $reqCode);
		$input = [
			'id' => $codeObj->aid,
			'request_serial' => $codeObj->serial,
			'request_code' => $reqCode,
			'fm_date' => $codeObj->fm_date,
			'to_date' => $codeObj->to_date,
			'code' => $ansCode,
		];
		$data = Models\LnsAnswer::create($input);
		return $data->code;
	}
	public static function issueRegist($reqCode, $ansCode, Request $request) {
		$reqLns = Models\LnsRequest::where('code', $reqCode)->first();
		if (!$reqLns) {
			throw new \Exception("code is not regist", 1);
		}
		$ansObj = json_decode(Common::DecryptDES($ansCode, $reqCode));
		if (!$ansObj || empty($ansObj->id)) {
			throw new \Exception("ans is bad", 1);
		}
		if ($ansObj->serial !== $reqLns->serial || $ansObj->id !== $reqLns->id) {
			throw new \Exception("ans is not pass", 1);
		}
		$input = [
			'id' => $ansObj->aid,
			'request_serial' => $ansObj->serial,
			'request_code' => $reqCode,
			'fm_date' => $ansObj->fm_date,
			'to_date' => $ansObj->to_date,
			'answer_code' => $ansCode,
			'content' => $reqLns->content,
		];
		$data = Models\Lns::updateOrCreate(['request_code' => $input['request_code']], $input);

		if ($data && $request && GAuth::entId()) {
			Models\EntLns::updateOrCreate(
				['ent_id' => GAuth::entId()],
				['revoked' => '0', 'lns_id' => $data->id]);
		}
		return true;
	}
}
