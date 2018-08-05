<?php

namespace Gmf\Sys\Models\Sv;

use Gmf\Sys\Database\Concerns\BatchImport;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Validator;

class Serve extends Model {
  use Snapshotable, HasGuard, BatchImport;
  protected $table = 'gmf_sys_sv_serves';
  public $incrementing = false;
  protected $keyType = 'string';
  protected $fillable = ['id', 'pack_id', 'code', 'name', 'method', 'path', 'discover', 'gateway', 'memo', 'content', 'released', 'revoked'];

  public function formatDefaultValue($attrs) {
    if (empty($this->path)) {
      $this->path = '';
    }
    if (empty($this->revoked)) {
      $this->revoked = 0;
    }
    if (empty($this->released)) {
      $this->released = 0;
    }
    if (empty($this->pack_id) && !empty($attrs['app']) && $v = $attrs['app']) {
      if ($v = InputHelper::tryGetObjectValue($v, 'code')) {
        $this->pack_id = Pack::where('code', $v)->value('id');
      }
    }
  }
  public function validate() {
    Validator::make($this->toArray(), [
      'pack_id' => 'required',
      'code' => ['required'],
      'name' => ['required'],
    ])->validate();
  }
  public function uniqueQuery($query) {
    $query->where([
      'pack_id' => $this->pack_id,
      'code' => $this->code,
    ]);
  }
  public static function fromImport($datas) {
    $datas = $datas->map(function ($row) {
      return $row;
    });
    static::BatchImport($datas);
  }
}
