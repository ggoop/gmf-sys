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
    //获取可填充字段
    $fill = $m->getFillable();
    //是否需要自动生成ID
		$needId = !$m->getIncrementing();
		$resultIds=[];
		$datas = $datas->map(function ($item) use ($m, $needId, $fill,&$resultIds,$canReplace) {
      $arrs = [];
      //动态创建模型
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
      //调用默认值处理
			if (method_exists($instance, 'formatDefaultValue')) {
				$instance->formatDefaultValue($arrs);
      }
      //模型校验
			if (method_exists($instance, 'validate')) {
				$instance->validate();
      }
      //校验是否存在
			if ($instance->exists()) {
				if(!$canReplace){
					throw new \Exception('已存在!');
				}
				$instance->save();
				$resultIds[]=$instance->id;
				return false;
      }
      //生成实例数据
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
    //分批批量创建
		$chunks = $datas->chunk(500)->toArray();
		foreach ($chunks as $key => $value) {
			DB::table($m->getTable())->insert($value);
		}
		return count($resultIds)?$resultIds:false;
	}
}