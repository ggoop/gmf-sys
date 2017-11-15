<?php

namespace Gmf\Sys\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class EntityField extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_entity_fields';
	public $incrementing = false;
	protected $keyType = 'string';
	protected $fillable = ['id', 'name', 'comment', 'field_name', 'entity_id', 'type_id',
		'type_type', 'type_enum', 'collection', 'sequence', 'default_value',
		'foreign_key', 'local_key', 'nullable', 'length', 'scale', 'precision',
		'format', 'former'];
	protected $hidden = ['created_at', 'updated_at'];
	public function type() {
		return $this->belongsTo('Gmf\Sys\Models\Entity');
	}
	public function entity() {
		return $this->belongsTo('Gmf\Sys\Models\Entity');
	}
	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id',
				'name', 'comment', 'field_name',
				'entity_id', 'type_id', 'type_type', 'type_enum',
				'collection', 'sequence', 'default_value', 'scale', 'precision',
				'foreign_key', 'local_key', 'nullable', 'length', 'format', 'former']);
			if (!empty($builder->entity)) {
				$entity = Entity::where('name', $builder->entity)->first();
				if (!empty($entity)) {
					$data['entity_id'] = $entity->id;
				}
				if (empty($data['entity_id'])) {
					$data['entity_id'] = $builder->entity;
				}
			}
			if (!empty($builder->type)) {
				$entity = Entity::where('name', $builder->type)->first();
				if (!empty($entity)) {
					$data['type_id'] = $entity->id;
					$data['type_type'] = $entity->name;
					$data['type_enum'] = $entity->type;
				}
				if (empty($data['type_type'])) {
					$data['type_type'] = $builder->type;
				}
			}
			$find = [];
			if (!empty($data['id'])) {
				$find['id'] = $data['id'];
			} else {
				$find['entity_id'] = $data['entity_id'];
				$find['name'] = $data['name'];
			}
			static::updateOrCreate($find, $data);
		});
	}
}
