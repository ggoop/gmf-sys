<?php

namespace Gmf\Sys\Libs;

class TreeBuilder {
	public static function create($data) {
		$builder = [];
		foreach ($data as $key => $value) {
			if (empty($value->parent_id) || $value->parent_id == $value->id) {
				static::fillChild($value, $data);
				$builder[] = $value;
			}
		}
		return $builder;
	}
	private static function fillChild($menu, $allMenus) {
		$childs = [];
		foreach ($allMenus as $key => $value) {
			if ($menu->id == $value->parent_id && $menu->id != $value->id) {
				static::fillChild($value, $allMenus);
				$childs[] = $value;
			}
		}
		if (count($childs) > 0) {
			$menu->childs = $childs;
		} else {
			$menu->childs = [];
		}
	}
}