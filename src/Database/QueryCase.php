<?php

namespace Gmf\Sys\Database;
use Gmf\Sys\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QueryCase {
	public static function formatRequest(Request $request) {
		$qc = new Builder;
		$temps = $request->input('wheres');
		$temps = json_decode(json_encode($temps));
		$wheres = [];
		//Log::error($temps);
		if ($temps) {
			foreach ($temps as $key => $value) {
				$item = new Builder;
				$item->name($value->name);
				if (empty($value->operator)) {
					if (!empty($value->value) && is_array($value->value)) {
						$item->operator('in');
					} else if (!empty($value->value)) {
						$item->operator('equal');
					} else {
						continue;
					}
				} else {
					$item->operator($value->operator);
				}
				if (!empty($value->value)) {
					$item->value($value->value);
				}
				//值为空，条件无效
				if (!($item->operator == 'null' || $item->operator == 'not_null') && empty($value->value)) {
					continue;
				}
				array_push($wheres, $item);
			}
		}
		$qc->wheres($wheres);
		Log::error($qc);
		return $qc;
	}
	/**
	 * [attachWhere description]
	 * @param  [type] $query     [description]
	 * @param  [type] $caseWhere {name,value,operator}
	 * @param  string $boolean   [description]
	 * @param  string $column    [description]
	 * @return [type]            [description]
	 */
	public static function attachWhere($query, $caseWhere, $column = '', $boolean = 'and') {
		if (empty($query) || empty($caseWhere)) {
			return $query;
		}
		if (empty($boolean)) {
			$boolean = 'and';
		}

		if (empty($column)) {
			$column = $caseWhere->name;
		}
		$value = $caseWhere->value;
		if (($caseWhere->operator == 'equal' || $caseWhere->operator == '=') && !empty($value)) {
			$query->where($column, $value);
		}
		if (($caseWhere->operator == 'not_equal' || $caseWhere->operator == '!=' || $caseWhere->operator == '<>') && !empty($value)) {
			$query->where($column, '!=', $value);
		}
		if (($caseWhere->operator == 'greater_than' || $caseWhere->operator == '>') && !empty($value)) {
			$query->where($column, '>', $value);
		}
		if (($caseWhere->operator == 'less_than' || $caseWhere->operator == '<') && !empty($value)) {
			$query->where($column, '<', $value);
		}
		if (($caseWhere->operator == 'greater_than_equal' || $caseWhere->operator == '>=') && !empty($value)) {
			$query->where($column, '>=', $value);
		}
		if (($caseWhere->operator == 'less_than_equal' || $caseWhere->operator == '<=') && !empty($value)) {
			$query->where($column, '<=', $value);
		}
		if ($caseWhere->operator == 'between' && !empty($value) && is_array($value)) {
			$query->whereBetween($column, $value);
		}
		if ($caseWhere->operator == 'not_between' && !empty($value) && is_array($value)) {
			$query->whereNotBetween($column, $value);
		}
		if ($caseWhere->operator == 'in' && !empty($value) && is_array($value)) {
			$query->whereIn($column, $value);
		}
		if ($caseWhere->operator == 'not_in' && !empty($value) && is_array($value)) {
			$query->whereNotIn($column, $value);
		}
		if ($caseWhere->operator == 'null') {
			$query->whereNull($column);
		}
		if ($caseWhere->operator == 'not_null') {
			$query->whereNotNull($column);
		}
		if ($caseWhere->operator == 'like' && !empty($value)) {
			$query->where($column, 'like', '%' . $value . '%');
		}
		if ($caseWhere->operator == 'left_like' && !empty($value)) {
			$query->where($column, 'like', '%' . $value);
		}
		if ($caseWhere->operator == 'right_like' && !empty($value)) {
			$query->where($column, 'like', $value . '%');
		}
		if ($caseWhere->operator == 'not_like' && !empty($value)) {
			$query->where($column, 'not like', '%' . $value . '%');
		}
		return $query;
	}
}