<?php

namespace Gmf\Sys\Database\Concerns;
use DB;
use Uuid;

trait BatchImport {
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
			$instance = new static($item);
			if (method_exists($instance, 'formatDefaultValue')) {
				$instance->formatDefaultValue($item);
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
		});

		$chunks = $datas->chunk(500)->toArray();
		foreach ($chunks as $key => $value) {
			DB::table($m->getTable())->insert($value);
		}
		return true;
	}
}