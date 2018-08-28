<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Database\Concerns\BatchImport;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Uuid;
use Validator;

class GroupCategory extends Model {
  use Snapshotable, HasGuard, BatchImport;
  protected $table = 'gmf_sys_group_categories';
  public $incrementing = false;
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['id', 'user_id', 'ent_id', 'group_id', 'root_id', 'parent_id', 'code', 'name', 'tag', 'memo', 'is_system', 'sequence', 'revoked'];
  public function group() {
    return $this->belongsTo('Gmf\Sys\Models\GroupItem');
  }
  public function root() {
    return $this->belongsTo('Gmf\Sys\Models\GroupCategory');
  }
  public function parent() {
    return $this->belongsTo('Gmf\Sys\Models\GroupCategory');
  }

  public function formatDefaultValue($attrs) {
    if (empty($this->code)) {
      $this->code = Uuid::generate();
    }
    if (empty($this->is_system)) {
      $this->is_system = 0;
    }
    if (empty($this->revoked)) {
      $this->revoked = 0;
    }
    if (empty($this->group_id) && !empty($attrs['group']) && $v = $attrs['group']) {
      if ($v = InputHelper::tryGetObjectValue($v, 'code')) {
        $this->group_id = GroupItem::where('code', $v)->where('ent_id', $this->ent_id)->value('id');
      }
    }
    if (empty($this->parent_id) && !empty($attrs['parent']) && $v = $attrs['parent']) {
      if ($v = InputHelper::tryGetObjectValue($v, 'code')) {
        $p = static::where('code', $v)->where('ent_id', $this->ent_id)->where('group_id', $this->group_id)->first();
        if ($p) {
          $this->parent_id = $p->id;
        }
      }
    }
  }
  public function validate() {
    Validator::make($this->toArray(), [
      'code' => ['required'],
      'name' => ['required'],
      'group_id' => ['required'],
    ])->validate();
  }
  public function uniqueQuery($query) {
    $query->where(function ($query) {
      $query->where('code', $this->code)->where('group_id', $this->group_id);
    });
  }
}
