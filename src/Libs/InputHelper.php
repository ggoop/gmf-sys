<?php

namespace Gmf\Sys\Libs;
use Illuminate\Http\Request;

class InputHelper {
	public static function fillEntity(Request $request, array $data, array $names = []) {
		foreach ($names as $key => $value) {
			$oid = $request->input($value . '.id');
			if ($oid) {
				$data[$value . '_id'] = $oid;
			}
		}
		return $data;
	}
	public static function fillEnum(Request $request, array $data, array $names = []) {
		foreach ($names as $key => $value) {
			$oid = $request->input($value . '.name');
			if ($oid) {
				$data[$value . '_enum'] = $oid;
			}
		}
		return $data;
	}
}