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
	protected $fillable = ['id', 'entity_id', 'name', 'comment', 'memo'];
	protected $hidden = ['created_at', 'updated_at'];
	public function fields() {
		return $this->hasMany('Gmf\Sys\Models\QueryField');
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
			if (empty($builder->comment) && $entity) {
				$builder->comment = $entity->comment;
			}
			$data = $builder->toArray();
			static::create(array_only($data, ['id', 'entity_id', 'name', 'comment', 'memo', 'matchs', 'filter']));
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
		});
	}
}
