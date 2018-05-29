<?php

namespace Gmf\Sys\Database\Concerns;
use DB;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Uuid;
trait BatchImport {
	/**
	$datas:array[array|model],collect([array|model])
	 */
	public static function BatchImport($datas) {
		if (is_array($datas)) {
			$datas = collect($datas);
		}
		if (empty($datas) || $datas->count() <= 0) {
			return false;
		}
		$m = new static();

		$fill = $m->getFillable();
		$needId = !$m->getIncrementing();
		$datas = $datas->map(function ($item) use ($m, $needId, $fill) {
			$arrs = [];
			if ($item instanceof Model) {
				$arrs = $item->toArray();
				$instance = $item;
			} else if (is_array($item)) {
				$arrs = $item;
				$instance = new static($arrs);
			} else if ($item instanceof Arrayable) {
				$arrs = $item->toArray();
				$instance = new static($arrs);
			} else {
				return false;
			}
			if (method_exists($instance, 'formatDefaultValue')) {
				$instance->formatDefaultValue($arrs);
			}
			if (method_exists($instance, 'validate')) {
				$instance->validate();
			}
			$item = $instance->toArray();
			unset($instance);
			$data = [];
			foreach ($fill as $key) {
				$data[$key] = data_get($item, $key);
			}
			if ($needId && empty($data[$m->getKeyName()])) {
				$data[$m->getKeyName()] = Uuid::generate();
			}
			return $data;
		})->reject(function ($name) {
			return empty($name);
		});

		$chunks = $datas->chunk(500)->toArray();
		foreach ($chunks as $key => $value) {
			DB::table($m->getTable())->insert($value);
		}
		return true;
	}
}