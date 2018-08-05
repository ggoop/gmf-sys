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
	protected $fillable = ['id', 'name', 'comment', 'table_name', 'type', 'connection','model'];
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
				$query->where('el.name', $name)->orWhere('el.default_value', $name)->orWhere('el.comment', $name);
			});
		return $query->value('el.name');
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
		$data = array_only($builder->toArray(), ['id', 'name', 'comment', 'table_name', 'type', 'connection','model']);

		$old = false;
		if (!empty($data['id']) || !empty($data['name'])) {
			$query = static::where('id', '!=', '');
			if (!empty($data['id']) && !empty($data['name'])) {
				$query->where(function ($query) use ($data) {
					$query->where('name', $data['name'])->orWhere('id', $data['id']);
				});
			} else if (!empty($data['id'])) {
				$query->where('id', $data['id']);
			} else if (!empty($data['name'])) {
				$query->where('name', $data['name']);
			}
			$old = $query->first();
		}
		if ($old) {
			return static::updateOrCreate(['id' => $old->id], $data);
		} else {
			return static::create($data);
		}
	}
}
