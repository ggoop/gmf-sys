<?php
namespace Gmf\Sys\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Query extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_queries';
	public $incrementing = false;
	protected $keyType = 'string';
	protected $fillable = ['id', 'component_id', 'entity_id', 'name', 'comment', 'memo', 'type_enum', 'size'];
	protected $hidden = ['created_at', 'updated_at'];

	public function component() {
		return $this->belongsTo('Gmf\Sys\Models\Component');
	}
	public function fields() {
		return $this->hasMany('Gmf\Sys\Models\QueryField');
	}
	public function wheres() {
		return $this->hasMany('Gmf\Sys\Models\QueryWhere');
	}
	public function orders() {
		return $this->hasMany('Gmf\Sys\Models\QueryOrder');
	}
	public function entity() {
		return $this->belongsTo('Gmf\Sys\Models\Entity');
	}
	/**
	 * add new model data
	 *  id(string),entity(string),fields([]),orders([])
	 * @param  Closure $callback [description]
	 * @return [type]            [description]
	 */
	public static function build(Closure $callback) {
		//id,root,parent,code,name,memo,uri,sequence
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			$entity = false;
			if (!empty($builder->entity_id)) {
				$entity = Entity::where('id', $builder->entity_id)->first();
			} else if (!empty($builder->entity)) {
				$entity = Entity::where('name', $builder->entity)->first();
			}
			if ($entity) {
				$builder->entity_id = $entity->id;
			}
			$component = false;
			if (!empty($builder->component_id)) {
				$component = Component::where('id', $builder->component_id)->first();
			} else if (!empty($builder->component)) {
				$component = Component::where('code', $builder->component)->first();
			}
			if ($component) {
				$builder->component_id = $component->id;
			}
			if (empty($builder->comment) && $entity) {
				$builder->comment = $entity->comment;
			}
			if (empty($builder->comment) && $component) {
				$builder->comment = $component->name;
			}
			$data = array_only($builder->toArray(), ['id', 'component_id', 'entity_id', 'name', 'comment', 'memo', 'type_enum', 'matchs', 'filter', 'size']);

			$find = array_only($data, ['name']);
			$main = static::updateOrCreate($find, $data);

			QueryField::where('query_id', $main->id)->delete();

			if (!empty($builder->fields) && is_array($builder->fields)) {
				foreach ($builder->fields as $key => $value) {
					$field = ['query_id' => $builder->id];
					if (is_string($key)) {
						$field['name'] = $key;
						if (is_array($value)) {
							if (!empty($value['comment'])) {
								$field['comment'] = $value['comment'];
							} else {
								$field['comment'] = $key;
							}
							if (!empty($value['sequence'])) {
								$field['sequence'] = $value['sequence'];
							}
							if (!empty($value['hide'])) {
								$field['hide'] = $value['hide'];
							}
						} else if (is_string($value)) {
							$field['comment'] = $value;
						}
					} else {
						$field['name'] = $value;
						$field['comment'] = $value;
					}
					QueryField::create($field);
				}
			}
			QueryOrder::where('query_id', $main->id)->delete();
			if (!empty($builder->orders) && is_array($builder->orders)) {
				foreach ($builder->orders as $key => $value) {
					$field = ['query_id' => $builder->id];
					if (is_string($key)) {
						$field['name'] = $key;
						$field['direction'] = $value;
					} else {
						$field['name'] = $value;
					}
					QueryOrder::create($field);
				}
			}
		});
	}
}
