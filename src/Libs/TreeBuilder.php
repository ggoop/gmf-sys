<?php

namespace Gmf\Sys\Libs;
use Gmf\Sys\Builder;

class TreeBuilder {
	public static function create($data) {
		$builder = [];
		foreach ($data as $key => $value) {
			if (!$value->parent_id || $value->parent_id == $value->id) {
				static::fillChild($value, $data);
				$builder[] = $value;
			}
		}
		return $builder;
	}
	private static function formatMenu($value) {
		$item = new Builder();
		$item->id($value->id)->code($value->code)->name($value->name)->uri($value->uri);
		if (isset($value->childs)) {
			$item->childs($value->childs);
		}
		return $item;
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