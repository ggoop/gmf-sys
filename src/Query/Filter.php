<?php

namespace Gmf\Sys\Query;
use Gmf\Sys\Builder;
use Illuminate\Support\Facades\Log;

class Filter {

/**
"wheres": {
"f1": {"name": "f1","value": "001","boolean":"or"},
"and":[{"name": "f3","value": "001"},{"name": "f4","value": "001"}],
"or": [
{"name": "f5","value": "001"},
{"and": [{"name": "code","value": "001"},{"name": "code","value": "001"}]},
{"or": [{"name": "code","value": "001"},{"name": "code1","value": "001"}]}
],
"other or and":{"boolean":"or","f6":{"name": "f6","value": "001"},"f7":{"name": "f7","value": "001"}},
"boolean":"and"
}

 */
	protected $items = [];
	public static function create($datas = null) {
		return new Filter($datas);
	}
	public function __construct($datas = null) {

	}
	private function isFilterItem($key, $value) {
		if (empty($value)) {
			return false;
		}
		if (is_object($value) && !empty($value->name) && is_string($value->name)) {
			return true;
		}

		return false;
	}
	private function isBooleanItem($key, $value) {
		if (empty($value)) {
			return false;
		}
		if ($key === 'and' && is_array($value)) {
			return 'and';
		}
		if ($key === 'or' && is_array($value)) {
			return 'or';
		}
		return false;
	}
	private function getBooleanExp($value, $default) {
		if (is_object($value) && !empty($value->boolean) && ($value->boolean === 'and' || $value->boolean === 'or')) {
			return $value->boolean;
		}
		return $default;
	}
	protected function parseItem($value) {
		$item = new Builder;

		$item->name($value->name);

		if (empty($value->operator)) {
			if (!empty($value->value) && is_array($value->value)) {
				$item->operator('in');
			} else if (!empty($value->value)) {
				$item->operator('equal');
			} else {
				return null;
			}
		} else {
			$item->operator($value->operator);
		}
		if (!empty($value->value)) {
			$item->value($value->value);
		}

		//值为空，条件无效
		if (!($item->operator == 'null' || $item->operator == 'not_null') && empty($value->value)) {
			return null;
		}
		return $item;
	}
	protected function _parse($items = null, Builder $where, $boolean = 'and') {
		$wheres = [];
		$hasItem = false;
		foreach ($items as $key => $value) {
			if (is_bool($value) || empty($value) || !(is_object($value) || is_array($value))) {
				continue;
			}
			if ($this->isFilterItem($key, $value)) {
				$item = $this->parseItem($value);
				if ($item) {
					$item->type('item');
					array_push($wheres, $item);
					continue;
				}
			} else if ($b = $this->isBooleanItem($key, $value)) {
				$item = new Builder;
				$hasItem = $this->_parse($value, $item, $b);
				if ($hasItem) {
					$item->type('boolean');
					array_push($wheres, $item);
					continue;
				}

			} else {
				$item = new Builder;
				$b = $this->getBooleanExp($value, $boolean);
				$hasItem = $this->_parse($value, $item, $b);
				if ($hasItem) {
					$item->type('boolean');
					array_push($wheres, $item);
					continue;
				}
			}
		}
		$where->boolean($boolean);
		$where->items($wheres);

		return count($wheres) > 0 ? $wheres : false;
	}
	public function parse($items = null) {
		$this->items = [];
		if (!is_object($items)) {
			$items = json_decode(json_encode($items));
		}
		if ($items) {
			$boolean = $this->getBooleanExp($items, false);
			if ($boolean) {
				$wrap = new Builder;
				$wrap->type('boolean');
				$this->_parse($items, $wrap, $boolean);
				$this->items[] = $wrap;
			} else {
				$wrap = new Builder;
				$wrap->type('boolean');
				$this->_parse($items, $wrap, $boolean);
				$this->items = $wrap->items;
			}
		}
		Log::error('parse filter');
		return $this->items;
	}
	public function getItems() {
		return $this->items;
	}
}