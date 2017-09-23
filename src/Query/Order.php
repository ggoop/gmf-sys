<?php

namespace Gmf\Sys\Query;
use Gmf\Sys\Builder;

class Order {

/**
"orders": {
"f1": "desc",
"f2":"asc"
}

"orders": ["f1",{"name":"f2"},{"name":"f2","direction":"desc"}]

 */
	protected $items = [];
	public static function create($datas = null) {
		return new Order($datas);
	}
	public function __construct($datas = null) {

	}
	protected function _parse($items = null) {
		$result = [];
		foreach ($items as $key => $value) {
			if (is_string($key) && is_string($value)) {
				$item = new Builder;
				$item->name($key)->direction($value);
				$result[] = $item;
			}
			if (is_object($value) && !empty($value->name)) {
				$item = new Builder;
				$item->name($value->name);
				if (!empty($value->direction)) {
					$item->direction($value->direction);
				}
				$result[] = $item;
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