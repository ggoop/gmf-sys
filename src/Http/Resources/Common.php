<?php
namespace Gmf\Sys\Http\Resources;

class Common {
	public static function toField($res, &$result, $field = []) {
		foreach ($field as $key => $value) {
			if (!empty($res->{$value})) {
				if ($value == 'created_at') {
					$result[$value] = $res->{$value} . '';
				} else {
					$result[$value] = $res->{$value};
				}
			}
		}
	}
	public static function toArrayField($res, &$result, $field = []) {
		foreach ($field as $key => $value) {
			if (!empty($res->{$value})) {
				if (is_string($res->{$value})) {
					$result[$value] = explode(',', $res->{$value});
				} else {
					$result[$value] = $res->{$value};
				}

			}
		}
	}
	public static function toBooleanField($res, &$result, $field = []) {
		foreach ($field as $key => $value) {
			if (!empty($res->{$value})) {
				$result[$value] = boolval($res->{$value});
			}
		}
	}
	public static function toIntField($res, &$result, $field = []) {
		foreach ($field as $key => $value) {
			if (!empty($res->{$value})) {
				$result[$value] = intval($res->{$value});
			}
		}
	}
}
