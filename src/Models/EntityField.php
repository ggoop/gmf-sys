<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class EntityField extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_entity_fields';
	public $incrementing = false;
	protected $keyType = 'string';
	protected $fillable = ['id', 'name', 'comment', 'field_name', 'entity_id', 'type_id',
		'type_type', 'collection', 'sequence', 'dValue',
		'foreign_key', 'local_key'];
	protected $hidden = ['created_at', 'updated_at'];
	public function type() {
		return $this->belongsTo('Gmf\Sys\Models\Entity');
	}
	public function entity() {
		return $this->belongsTo('Gmf\Sys\Models\Entity');
	}
}
