<?php

namespace Gmf\Sys\Models\Sv;

use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use GAuth;
use Gmf\Sys\Database\Concerns\BatchImport;
use Validator;
use Uuid;

class Pack extends Model
{
  use Snapshotable, HasGuard, BatchImport;
  protected $table = 'gmf_sys_sv_packs';
  public $incrementing = false;
  protected $keyType = 'string';
  protected $fillable = ['id','openid', 'type', 'code', 'name', 'avatar', 'discover', 'gateway', 'memo', 'content', 'released', 'revoked'];

  public function formatDefaultValue($attrs)
  {
    if (empty($this->openid)) {
      $this->openid = Uuid::generate();
    }
    if (empty($this->path)) {
      $this->path = '';
    }
    if (empty($this->revoked)) {
      $this->revoked = 0;
    }
    if (empty($this->released)) {
      $this->released = 0;
    }
  }
  public function validate()
  {
    Validator::make($this->toArray(), [
      'code' => ['required'],
      'name' => ['required'],
    ])->validate();
  }
  public function uniqueQuery($query)
  {
    $query->where([
      'code' => $this->code,
    ]);
  }
  public static function fromImport($datas)
  {
    $datas = $datas->map(function ($row) {
      return $row;
    });
    static::BatchImport($datas);
  }
}
