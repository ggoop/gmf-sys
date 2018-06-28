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

class Serve extends Model
{
  use Snapshotable, HasGuard, BatchImport;
  protected $table = 'gmf_sys_sv_serves';
  public $incrementing = false;
  protected $keyType = 'string';
  protected $fillable = ['id', 'pack_id', 'code', 'name', 'method', 'path','discover', 'gateway', 'memo', 'content', 'released', 'revoked'];

  public function formatDefaultValue($attrs)
  {
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
			$v = !empty($v['code']) ? $v['code'] : !empty($v->code) ? $v = $v->code : is_string($v) ? $v : false;
			$this->pack_id = Pack::where('code', $v)->value('id');
		}
  }
  public function validate()
  {
    Validator::make($this->toArray(), [
      'pack_id'=>'required',
      'code' => ['required'],
      'name' => ['required'],
    ])->validate();
  }
  public function uniqueQuery($query)
  {
    $query->where([
      'pack_id' => $this->pack_id,
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
