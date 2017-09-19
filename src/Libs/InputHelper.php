<?php

namespace Gmf\Sys\Libs;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class InputHelper {
	/**

	$names ['org','dept']
	$names ['org'=>'App\Org','dept']
	$names ['org'=>['type'=>'App\Org','matchs'=>['code']],'dept']
	$names ['org'=>['type'=>'App\Org','matchs'=>['code'=>'code','ent_id'=>'${ent_id}']],'dept']

	$context:['ent_id'=>123]
	 */
	public static function fillEntity(array $data, $inputs, array $names = [], array $context = []) {
		if ($inputs instanceof Collection) {
			$inputs = $inputs->all();
		}
		foreach ($names as $nk => $nv) {
			$field = $nv;
			$vType = false;
			$vMatchs = ['code'];
			//只有值名称时
			if (is_numeric($nk)) {
				$field = $nv;
			} else {
				$field = $nk;
				if (is_string($nk) && is_string($nv) && !empty($nv)) {
					$vType = $nv;
				}
				if (is_string($nk) && is_array($nv) && !empty($nv['type'])) {
					$vType = $nv['type'];
					if (!empty($nv['matchs'])) {
						$vMatchs = $nv['matchs'];
					}
				}
			}

			$vFieldName = 'id';
			if (array_has($inputs, $field)) {
				$data[$field . '_id'] = '';
			}

			$vObj = Arr::get($inputs, $field);

			if (empty($vObj)) {
				continue;
			}
			$vValue = false;
			//如果值对象中，存在该值，直接取
			if (!empty($vObj[$vFieldName])) {
				$vValue = $vObj[$vFieldName];
			}
			//如果值为空，且存在取则规则时
			if (empty($vValue) && !empty($vType) && !empty($vMatchs) && class_exists($vType)) {
				$query = $vType::select('id');
				foreach ($vMatchs as $mk => $mv) {
					$matchField = $mv;
					if (is_string($mk)) {
						$matchField = $mk;
					}
					$matchValue = '';
					if (starts_with($mv, '${') && !empty($context[str_replace('}', '', str_replace('${', '', $mv))])) {
						$matchValue = $context[str_replace('}', '', str_replace('${', '', $mv))];
					} else if (!empty($vObj[$matchField])) {
						$matchValue = $vObj[$matchField];
					}
					$query->where($matchField, $matchValue);
				}
				$vValue = $query->value('id');
				// if (is_a($vType, \Illuminate\Database\Eloquent\Model::class)) {
				// 	var_dump(class_exists($vType));
				// }
			}
			if ($vValue) {
				$data[$field . '_id'] = $vValue;
			}
			unset($data[$field]);
		}
		return $data;
	}
	public static function fillBoolean(array $data, $inputs, array $names = []) {
		if ($inputs instanceof Collection) {
			$inputs = $inputs->all();
		}
		foreach ($names as $key => $value) {
			$oid = Arr::get($inputs, $value);
			$data[$value] = !!$oid;
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
				unset($data[$value]);
			}
		}
		return $data;
	}
}