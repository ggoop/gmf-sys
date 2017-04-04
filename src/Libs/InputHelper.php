<?php

namespace Gmf\Sys\Libs;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class InputHelper {
	public static function fillEntity(array $data, $inputs, array $names = []) {
		if ($inputs instanceof Collection) {
			$inputs = $inputs->all();
		}
		foreach ($names as $key => $value) {
			$oid = Arr::get($inputs, $value . '.id');
			if ($oid) {
				$data[$value . '_id'] = $oid;
			}
		}
		return $data;
	}
	public static function fillEnum(array $data, $inputs, array $names = []) {
		if ($inputs instanceof Collection) {
			$inputs = $inputs->all();
		}
		foreach ($names as $key => $value) {
			$oid = Arr::get($inputs, $value . '.name');
			if ($oid) {
				$data[$value . '_enum'] = $oid;
			}
		}
		return $data;
	}
}