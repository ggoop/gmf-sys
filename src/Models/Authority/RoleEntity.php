<?php

namespace Gmf\Sys\Models\Authority;
use Gmf\Sys\Database\Concerns\BatchImport;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Models\Entity;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Validator;

class RoleEntity extends Model {
  use Snapshotable, HasGuard, BatchImport;
  protected $table = 'gmf_sys_authority_role_entities';

  protected $fillable = ['ent_id', 'role_id', 'entity_id', 'data_id', 'data_type', 'data_name', 'filter', 'memo', 'operation_enum'];

  public function role() {
    return $this->belongsTo('Gmf\Sys\Models\Authority\Role');
  }
  public function entity() {
    return $this->belongsTo('Gmf\Sys\Models\Entity');
  }
  public function data() {
    return $this->morphTo();
  }

  public function formatDefaultValue($attrs) {
    if (empty($this->revoked)) {
      $this->revoked = 0;
    }
    if (empty($this->role_id) && !empty($attrs['role']) && $v = $attrs['role']) {
      if ($v = InputHelper::tryGetObjectValue($v, 'code')) {
        $this->role_id = Role::where('code', $v)->where('ent_id', $this->ent_id)->value('id');
      }
    }
    if (empty($this->entity_id) && !empty($attrs['entity']) && $v = $attrs['entity']) {
      if ($v = InputHelper::tryGetObjectValue($v, 'name')) {
        $this->entity_id = Entity::where('name', $v)->orWhere('id', $v)->value('id');
      }
    }
  }
  public function validate() {
    Validator::make($this->toArray(), [
      'role_id' => ['required'],
      'ent_id' => ['required'],
      'entity_id' => ['required'],
    ])->validate();
  }
  public function uniqueQuery($query) {
    $query->where(function ($query) {
      $query->where('id', $this->id);
    });
  }
}
