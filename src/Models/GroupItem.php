<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Database\Concerns\BatchImport;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Uuid;
use Validator;

class GroupItem extends Model {
  use Snapshotable, HasGuard, BatchImport;
  protected $table = 'gmf_sys_group_items';
  public $incrementing = false;
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['id', 'user_id', 'ent_id', 'code', 'name', 'memo', 'is_system', 'revoked'];

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
  }
  public function validate() {
    Validator::make($this->toArray(), [
      'code' => ['required'],
    ])->validate();
  }
  public function uniqueQuery($query) {
    $query->where(function ($query) {
      $query->where('ent_id', $this->ent_id)->where('code', $this->code);
    });
  }
}
