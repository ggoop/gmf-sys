<?php

namespace Gmf\Sys\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Component extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_components';
	public $incrementing = false;
	protected $fillable = ['id', 'type_enum', 'name', 'code', 'memo', 'path'];

	public function setCodeAttribute($value) {
		$this->attributes['code'] = static::formatCode($value);
	}

	private static function formatCode($value) {
		$value = kebab_case($value);
		$value = str_replace('-', '.', $value);
		$value = str_replace('..', '.', $value);
		$value = strtolower($value);
		return $value;
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			$data = array_only($builder->toArray(), ['id', 'type_enum', 'name', 'code', 'memo', 'path']);
			$data['code'] = static::formatCode($data['code']);

			$find = array_only($data, ['code']);
			static::updateOrCreate($find, $data);
		});
	}
}
