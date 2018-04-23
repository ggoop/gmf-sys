<?php

namespace Gmf\Sys\Bp;
use Carbon\Carbon;
use Gmf\Sys\Models;
use Gmf\Sys\Uuid;

class VCode {
	public function generate($type, $input, $token = '') {
		$data = array_only($input, ['user_id', 'client_id', 'client_key', 'channel', 'expire_time']);
		$data['type'] = $type;
		$data['content'] = serialize($input);
		if (empty($token)) {
			$token = Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
		}
		$data['token'] = $token;
		$code = Models\VCode::create($data);
		return $code;
	}
	public function checker($id, $type) {
		$c = Models\VCode::where(['id' => $id, 'type' => $type])->first();
		if (empty($c)) {
			return false;
		}
		if ($c->expire_time < Carbon::now()) {
			return false;
		}
		return $c;
	}
	public function delete($id) {
		$ids = explode(",", $id);
		Models\VCode::whereIn('id', $ids)->delete();
		return true;
	}
}