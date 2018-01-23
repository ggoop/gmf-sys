<?php

namespace Gmf\Sys\Libs;
use Closure;
use DB;
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
			$callback = false;
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
				if (is_string($nk) && is_callable($nv)) {
					$callback = $nv;
				}
			}

			$vObj = Arr::get($inputs, $field);
			if (empty($vObj)) {
				$vObj = Arr::get($data, $field);
			}
			$vObj = json_decode(json_encode($vObj));
			unset($data[$field]);
			$vFieldName = 'id';
			if (array_has($inputs, $field)) {
				$data[$field . '_id'] = '';
			}
			$vValue = false;
			//如果值对象中，存在该值，直接取
			if (empty($vValue) && !empty($vObj) && !empty($vObj->{$vFieldName})) {
				$vValue = $vObj->{$vFieldName};
			}
			if (empty($vValue) && !empty($vObj) && is_array($vObj) && !empty($vObj[$vFieldName])) {
				$vValue = $vObj[$vFieldName];
			}
			if (empty($vValue) && !empty($vObj) && is_array($vObj)) {
				$vids = [];
				foreach ($vObj as $vk => $vv) {
					if (!empty($vv) && is_object($vv) && !empty($vv->{$vFieldName})) {
						$vids[] = $vv->{$vFieldName};
					} else if (!empty($vv) && is_array($vv) && !empty($vv[$vFieldName])) {
						$vids[] = $vv[$vFieldName];
					}
				}
				if ($vids && count($vids)) {
					$vValue = $vids;
				}
			}
			if (empty($vValue) && is_a($callback, Closure::class)) {
				$vValue = $callback($vObj, $data);
				unset($callback);
			}
			//如果值为空，且存在取则规则时
			if (empty($vValue) && !empty($vObj) && !empty($vType) && !empty($vMatchs) && class_exists($vType)) {
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
			}
			if ($vValue) {
				if (is_object($vValue) && !empty($vValue->id)) {
					$vValue = $vValue->id;
				} else if (is_array($vValue) && !empty($vValue['id'])) {
					$vValue = $vValue['id'];
				}
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
		foreach ($names as $nk => $nv) {
			$field = $nv;
			$vType = false;
			$callback = false;
			$vValue = false;
			if (is_numeric($nk)) {
				$field = $nv;
			} else {
				$field = $nk;
				if (is_string($nk) && is_string($nv) && !empty($nv)) {
					$vType = $nv;
				}
				if (is_string($nk) && is_callable($nv)) {
					$callback = $nv;
				}
			}
			$vObj = Arr::get($inputs, $field);
			if (empty($vObj)) {
				$vObj = Arr::get($data, $field);
			}
			if (empty($vValue) && !empty($vObj) && is_object($vObj) && !empty($vObj->name)) {
				$vValue = $vObj->name;
			} else if (empty($vValue) && !empty($vObj) && is_array($vObj) && !empty($vObj['name'])) {
				$vValue = $vObj['name'];
			}
			if (empty($vValue) && is_a($callback, Closure::class)) {
				$vValue = $callback($vObj, $data);
				unset($callback);
			} else if (empty($vValue) && !empty($vObj) && !empty($vType)) {
				$query = DB::table('gmf_sys_entities as e')
					->join('gmf_sys_entity_fields as el', 'e.id', '=', 'el.entity_id')
					->select('el.name', 'el.comment', 'el.default_value')
					->where('e.name', $vType)
					->where(function ($query) use ($vObj) {
						$query->where('el.name', $vObj)
							->orWhere('el.default_value', $vObj)
							->orWhere('el.comment', $vObj);
					});
				$vValue = $query->value('name');
			}
			if (empty($vValue)) {
				$vValue = Arr::get($data, $field . '_enum');
			}
			if (empty($vValue)) {
				$vValue = Arr::get($inputs, $field . '_enum');
			}
			if (empty($vValue) && !empty($vObj) && is_string($vObj)) {
				$vValue = $vObj;
			}
			if ($vValue) {
				if (is_object($vValue) && !empty($vValue->name)) {
					$vValue = $vValue->name;
				} else if (is_array($vValue) && !empty($vValue['name'])) {
					$vValue = $vValue['name'];
				}
				$data[$field . '_enum'] = $vValue;
				unset($data[$field]);
			} else {
				unset($data[$field . '_enum']);
			}
		}
		return $data;
	}
}