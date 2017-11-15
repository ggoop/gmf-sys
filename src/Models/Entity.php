<?php

namespace Gmf\Sys\Models;
use Closure;
use DB;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_entities';
	public $incrementing = false;
	protected $keyType = 'string';
	protected $fillable = ['id', 'name', 'comment', 'table_name', 'type'];
	protected $hidden = ['created_at', 'updated_at'];
	public function fields() {
		return $this->hasMany('Gmf\Sys\Models\EntityField');
	}
	public static function getEnumValue($type, $name, $opts = []) {
		$query = DB::table('gmf_sys_entities as e')
			->join('gmf_sys_entity_fields as el', 'e.id', '=', 'el.entity_id')
			->select('el.name', 'el.comment', 'el.default_value')
			->where('e.name', $type)
			->where(function ($query) use ($name) {
				$query->where('el.name', $name)->orWhere('el.default_value', $name);
			});
		$p = $query->first();
		return $p;
	}
	public static function getEnumItem($type, $name, $opts = []) {
		$query = DB::table('gmf_sys_entities as e')
			->join('gmf_sys_entity_fields as el', 'e.id', '=', 'el.entity_id')
			->select('el.name', 'el.comment', 'el.default_value')
			->where('e.name', $type)
			->where(function ($query) use ($name) {
				$query->where('el.comment', $name)->orWhere('el.name', $name)->orWhere('el.default_value', $name);
			});
		$p = $query->first();
		return $p;
	}
	public static function build(Closure $callback) {
		$builder = new Builder;
		$callback($builder);
		$data = array_only($builder->toArray(), ['id', 'name', 'comment', 'table_name', 'type']);
		$find = [];
		if (!empty($data['id'])) {
			$find['id'] = $data['id'];
		}
		if (!empty($data['name'])) {
			$find['name'] = $data['name'];
		}
		return static::updateOrCreate($find, $data);

	}
}
