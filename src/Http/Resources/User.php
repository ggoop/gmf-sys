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
			'account' => $this->account,
			'nick_name' => $this->nick_name,
			'avatar' => $this->avatar,
			'cover' => $this->cover,
			'memo' => $this->memo,
			$this->mergeWhen($this->id === GAuth::id(), [
				'mobile' => $this->mobile,
				'email' => $this->email,
				'account' => $this->account,
				'is_me' => true,
			]),
		];
		if (!empty($this->pivot)) {
			$rtn['created_at'] = $this->pivot->created_at . '';
		}
		return $rtn;
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
