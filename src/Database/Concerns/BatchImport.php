<?php

namespace Gmf\Sys\Database\Concerns;
use DB;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Log;
use Uuid;

trait BatchImport {
	public function exists() {
		if (method_exists(static::class, 'uniqueQuery')) {
			$query = static::query();
			$d = $this->uniqueQuery($query);
			if (is_bool($d)) {
				return $d;
			}
			if ($d && $d instanceof Model) {
				$this->exists = true;
				$this->{$this->getKeyName()} = $d->{$d->getKeyName()};
			} else if (is_null($d) && $query->exists()) {
				$this->exists = true;
				$this->{$this->getKeyName()} = $query->value($this->getKeyName());
			}
			if($this->exists){
				Log::error(static::class . ' is exists:' . $this->{$this->getKeyName()});
			}	
			return $this->exists;
		}
		return false;
	}
	public static function createFromFill($data){
		$instance = new static();
		$instance->fillData($data);
		return $instance;
	}
	public  function fillData($data){
		$this->fill($data);
		if (method_exists($this, 'formatDefaultValue')) {
			$this->formatDefaultValue($data);
		}
		return $this;
	}
	/**
	*$datas:array[array|model],collect([array|model])
	 */
	public static function BatchImport($datas,$canReplace=true) {
		if (is_array($datas)) {
			$datas = collect($datas);
		}
		if (empty($datas) || $datas->count() <= 0) {
			return false;
		}
		$m = new static();

		$fill = $m->getFillable();
		$needId = !$m->getIncrementing();
		$resultIds=[];
		$datas = $datas->map(function ($item) use ($m, $needId, $fill,&$resultIds,$canReplace) {
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
			if ($instance->exists()) {
				if(!$canReplace){
					throw new \Exception('已存在!');
				}
				$instance->save();
				$resultIds[]=$instance->id;
				return false;
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
			$resultIds[]=$data[$m->getKeyName()];
			return $data;
		})->reject(function ($name) {
			return empty($name);
		});

		$chunks = $datas->chunk(500)->toArray();
		foreach ($chunks as $key => $value) {
			DB::table($m->getTable())->insert($value);
		}
		return count($resultIds)?$resultIds:false;
	}
}