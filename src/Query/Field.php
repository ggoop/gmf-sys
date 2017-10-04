<?php

namespace Gmf\Sys\Query;
use Gmf\Sys\Builder;

class Field {

/**
"fields": {
"f1": "栏目1",
"f2":"栏目2"
}

"fields": [
{"name":"f2"},
{"name":"f2","comment":"栏目1"},
{"name":"f1","comment":"栏目1","hide":1},
]

 */
	protected $items = [];
	public static function create($datas = null) {
		return new Field($datas);
	}
	public function __construct($datas = null) {

	}
	protected function _parse($items = null) {
		$result = [];
		foreach ($items as $key => $value) {
			if (is_int($key) && is_string($value)) {
				$item = new Builder;
				$item->name($value);
				$result[] = $item;
				continue;
			}
			if (is_string($key) && is_string($value)) {
				$item = new Builder;
				$item->name($key)->comment($value);
				$result[] = $item;
				continue;
			}
			if (is_object($value) && !empty($value->name)) {
				$item = new Builder;
				$item->name($value->name);
				if (!empty($value->comment)) {
					$item->comment($value->comment);
				}
				if (!empty($value->hide)) {
					$item->hide($value->hide);
				}
				$result[] = $item;
				continue;
			}
		}
		return $result;
	}
	public function parse($items = null) {
		if (!is_object($items)) {
			$items = json_decode(json_encode($items));
		}
		if ($items) {
			$this->items = $this->_parse($items);
		}
		return $this->items;
	}
	public function getItems() {
		return $this->items;
	}
}