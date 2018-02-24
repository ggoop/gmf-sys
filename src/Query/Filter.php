<?php

namespace Gmf\Sys\Query;
use Gmf\Sys\Builder;

class Filter {

/*
wheres:{
"term":{},
"or":[],
"and":[],
"boolean":"and"
}
wheres:[]

{'name':'field','operator':'=','value':'value'}
{"or": [item1,item2,item3]}
{"or": {'field1':'234'}}

{"and": [item1,item2,item3]}
{"not":[item1,item2,item3]}

{"term": {"last_name" : "smith" }}
{"match" : {"last_name" : "smith" }}
{"left_match" : {"last_name" : "smith" }}
{"right_match" : {"last_name" : "smith" }}
{"gt" : {"age" : 30 }}
{"gtl" : {"age" : 30 }}
{"lt" : {"age" : 30 }}
{"lte" : {"age" : 30 }}
{"missing": "field"}
{"exists": "field"}

{'$key':{'term':{'field':'value'}}}
{'field':'value'}
 */
	protected $items = [];
	public static function create($datas = null) {
		return new Filter($datas);
	}
	public function __construct($datas = null) {

	}
	protected function parseItemValue($value) {
		if (empty($value)) {
			return false;
		}
		$rtn = false;
		if (is_object($value)) {
			if (!empty($value->id)) {
				$rtn = $value->id;
			}
		} else if (is_array($value)) {
			$rtn = [];
			foreach ($value as $k => $v) {
				if (is_object($v)) {
					if (!empty($v->id)) {
						$rtn[] = $v->id;
					}
				} else if (!is_array($v)) {
					$rtn[] = $v;
				}
			}
			return count($rtn) > 0 ? $rtn : false;
		} else {
			$rtn = $value;
		}
		return $rtn;
	}
	//and or
	protected function _parseBoolean(Array &$contains, $operator, $value = null, $boolean = 'and') {
		if (!is_array($value)) {
			return false;
		}

		$hasItem = false;
		$wheres = [];

		foreach ($value as $key => $item) {
			if ($hasItem = $this->_parse($item, $operator)) {
				$wheres = array_merge($wheres, $hasItem);
			}
		}
		if ($wheres && count($wheres) > 0) {
			$item = new Builder;
			$item->type('boolean');
			$item->boolean($boolean);
			$item->items($wheres);
			$contains[] = $item;
		}
	}
	protected function _parseItemNull(Array &$contains, $operator, $names = null, $boolean = 'and') {
		if (!is_string($names)) {return;}
		$item = new Builder;
		$item->type('item');
		$item->operator($operator);
		$item->boolean($boolean);
		$item->name($names);
		$contains[] = $item;
		return $item;
	}
	protected function _parseItemMatch(Array &$contains, $operator, $value = null, $boolean = 'and') {
		if (!is_object($value)) {return;}
		$wheres = [];
		$hasItem = false;
		foreach ($value as $pk => $pv) {
			$hasItem = $this->parseItemValue($pv);
			if ($hasItem == false || is_array($hasItem)) {
				continue;
			}
			$item = new Builder;
			$item->type('item');
			$item->operator($operator);
			$item->boolean('and');
			$item->name($pk);
			$item->value($hasItem);
			$wheres[] = $item;
		}
		if (count($wheres) == 1) {
			$wheres[0]->boolean($boolean);
			$contains[] = $wheres[0];
		} else if (count($wheres) > 1) {
			$item = new Builder;
			$item->type('boolean');
			$item->boolean($boolean);
			$item->items($wheres);
			$contains[] = $item;
		}
	}
	protected function _parseItemBetween(Array &$contains, $operator, $value = null, $boolean = 'and') {
		if (!is_object($value)) {return;}
		$wheres = [];
		$hasItem = false;
		foreach ($value as $pk => $pv) {
			if ($hasItem = $this->parseItemValue($pv) && is_array($hasItem) && count($hasItem) == 2) {
				$item = new Builder;
				$item->type('item');
				$item->operator($operator);
				$item->boolean('and');
				$item->name($pk);
				$item->value($hasItem);
				$wheres[] = $item;
			}
		}
		if (count($wheres) == 1) {
			$wheres[0]->boolean($boolean);
			$contains[] = $wheres[0];
		} else if (count($wheres) > 1) {
			$item = new Builder;
			$item->type('boolean');
			$item->boolean($boolean);
			$item->items($wheres);
			$contains[] = $item;
		}
	}
	protected function _parseItemTerms(Array &$contains, $operator, $value = null, $boolean = 'and') {
		if (!is_object($value)) {return;}
		$wheres = [];
		$hasItem = false;
		foreach ($value as $pk => $pv) {
			if ($hasItem = $this->parseItemValue($pv) && is_array($hasItem)) {
				$item = new Builder;
				$item->type('item');
				$item->operator($operator);
				$item->boolean('and');
				$item->name($pk);
				$item->value($hasItem);
				$wheres[] = $item;
			}
		}
		if (count($wheres) == 1) {
			$wheres[0]->boolean($boolean);
			$contains[] = $wheres[0];
		} else if (count($wheres) > 1) {
			$item = new Builder;
			$item->type('boolean');
			$item->boolean($boolean);
			$item->items($wheres);
			$contains[] = $item;
		}
	}
	/**
	$data={name:'',operator:''}
	 */
	protected function _parseCaseItem(Array &$contains, $data, $boolean = 'and') {
		if (empty($data->name) || empty($data->operator) || !is_string($data->name) || !is_string($data->operator)) {
			return;
		}
		if (in_array($data->operator, ['missing', 'exists', 'null', 'not_null'])) {
			$this->_parseItemNull($contains, $data->operator, $data->name, $boolean);
			return;
		}
		$iv = new \stdClass;
		if (!empty($data->value)) {
			$iv->{$data->name} = $data->value;
		}
		if (in_array($data->operator, [
			'term', 'equal', '=', 'match', 'like', 'gt', 'gte', 'lt', 'lte', '>', '>=', '<', '<=',
			'greater_than', 'less_than', 'greater_than_equal', 'less_than_equal',
			'left_match', 'right_match', 'left_like', 'right_like',
			'not_term', 'not_equal', '!=', '<>', 'not_match', 'not_like',
		])) {
			$this->_parseItemMatch($contains, $data->operator, $iv, $boolean);
			return;
		}
		if (in_array($data->operator, ['between', 'not_between'])) {
			$this->_parseItemBetween($contains, $data->operator, $iv, $boolean);
			return;
		}
		if (in_array($data->operator, ['terms', 'in', 'not_in'])) {
			$this->_parseItemTerms($contains, $data->operator, $iv, $boolean);
			return;
		}
	}

	protected function _parse($items = null, $boolean = 'and') {
		$wheres = [];
		$hasItem = false;
		$item = false;

		if (is_array($items)) {
			$this->_parseBoolean($wheres, $boolean, $items, $boolean);
			return count($wheres) > 0 ? $wheres : false;
		} else if (!is_object($items)) {
			return false;
		}
		if (!empty($items->name) && !empty($items->operator)) {
			$this->_parseCaseItem($wheres, $items, $boolean);
			return count($wheres) > 0 ? $wheres : false;
		}
		foreach ($items as $ik => $iv) {
			if (is_bool($iv) || empty($iv) || !(is_object($iv) || is_array($iv) || is_string($iv) || is_numeric($iv))) {
				continue;
			}
			if (in_array($ik, ['or', 'and'])) {
				$this->_parseBoolean($wheres, $ik, $iv, $boolean);
				continue;
			}
			if (in_array($ik, ['missing', 'exists', 'null', 'not_null'])) {
				$this->_parseItemNull($wheres, $ik, $iv, $boolean);
				continue;
			}
			if (in_array($ik, [
				'term', 'equal', '=', 'match', 'like', 'gt', 'gte', 'lt', 'lte', '>', '>=', '<', '<=',
				'greater_than', 'less_than', 'greater_than_equal', 'less_than_equal',
				'left_match', 'right_match', 'left_like', 'right_like',
				'not_term', 'not_equal', '!=', '<>', 'not_match', 'not_like',
			])) {
				$this->_parseItemMatch($wheres, $ik, $iv, $boolean);
				continue;
			}
			if (in_array($ik, ['between', 'not_between'])) {
				$this->_parseItemBetween($wheres, $ik, $iv, $boolean);
				continue;
			}
			if (in_array($ik, ['terms', 'in', 'not_in'])) {
				$this->_parseItemTerms($wheres, $ik, $iv, $boolean);
				continue;
			}
			if (starts_with($ik, '$')) {
				if ($hasItem = $this->_parse($iv, $boolean)) {
					$wheres = array_merge($wheres, $hasItem);
				}
				continue;
			}
			if (!in_array($ik, ['boolean']) && is_string($ik) && $hasItem = $this->parseItemValue($iv)) {
				$item = new Builder;
				$item->type('item');
				$item->operator('term');
				$item->boolean($boolean);
				$item->name($ik);
				$item->value($hasItem);
				$wheres[] = $item;
				continue;
			}
		}
		return count($wheres) > 0 ? $wheres : false;
	}
	public function parse($items = null) {
		$this->items = [];
		if (!is_object($items)) {
			$items = json_decode(json_encode($items));
		}
		if ($items) {
			$hasItem = false;
			$boolean = 'and';
			if (is_object($items)) {
				if (!empty($items->boolean)) {
					$boolean = $items->boolean;
				}
				if ($hasItem = $this->_parse($items, $boolean)) {
					$this->items = array_merge($this->items, $hasItem);
				}
			} else if (is_array($items)) {
				foreach ($items as $key => $value) {
					if ($hasItem = $this->_parse($value, $boolean)) {
						$this->items = array_merge($this->items, $hasItem);
					}
				}
			}
		}
		return $this->items;
	}
	public function getItems() {
		return $this->items;
	}
}