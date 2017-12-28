<?php

namespace Gmf\Sys\Http\Resources;

use GAuth;
use Illuminate\Http\Resources\Json\Resource;

class User extends Resource {
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return array
	 */
	public function toArray($request) {
		if (empty($this->id)) {
			return false;
		}
		$rtn = [
			'id' => $this->id,
			'name' => $this->name,
			'nick_name' => $this->nick_name,
			'avatar' => $this->avatar,
			'cover' => $this->cover,
			'type' => $this->type,
			'email_verified' => boolval($this->email_verified),
			'mobile_verified' => boolval($this->mobile_verified),
			'memo' => $this->memo,
		];
		if ($this->id == GAuth::id()) {
			$rtn['is_me'] = true;
			$rtn['mobile'] = $this->mobile;
			$rtn['email'] = $this->email;
			$rtn['account'] = $this->account;
		} else {
			$rtn['mobile'] = $this->hidePhone($this->mobile);
			$rtn['email'] = $this->hideEmail($this->email);
			$rtn['account'] = $this->hideAccount($this->account);
		}
		if (!empty($this->pivot)) {
			$rtn['created_at'] = $this->pivot->created_at . '';
		}
		return $rtn;
	}
	private function hideEmail($input) {
		if (!$input || !$this->isEmail($input)) {
			return $input;
		}
		$array = explode('@', $input);
		if (count($array) < 2) {
			return '***@***';
		}
		if (strlen($array[0]) > 5) {
			$array[0] = str_pad(substr($array[0], 0, 4), strlen($array[0]), '*');
		} else if (strlen($array[0]) > 2) {
			$array[0] = str_pad(substr($array[0], 0, 2), strlen($array[0]), '*');
		} else {
			$array[0] = str_pad('', strlen($array[0]), '*');
		}
		return implode('@', $array);
	}
	private function hideAccount($input) {
		if ($this->isEMail($input)) {
			return $this->hideEmail($input);
		} else if ($this->isPhone($input)) {
			return $this->hidePhone($input);
		} else {
			if (strlen($input) > 5) {
				return str_pad(substr($input, 0, 4), strlen($input), '*');
			}
			return str_pad('', strlen($input), '*');
		}
	}
	private function hidePhone($input) {
		if (!$input || !$this->isPhone($input)) {
			return $input;
		}
		return substr($input, 0, 3) . '****' . substr($input, 7);
	}
	private function isPhone($number) {
		return preg_match("/^1[34578]{1}\d{9}$/", $number);
	}
	private function isEmail($number) {
		return filter_var($number, FILTER_VALIDATE_EMAIL);
	}
	public static function anony() {
		return [
			'id' => '',
			'name' => '匿名',
			'nick_name' => '匿名',
			'memo' => '',
			'avatar' => '/assets/vendor/gmf-sys/avatar/' . rand(1, 10) . '.jpg',
		];
	}
}
