<?php

namespace Gmf\Sys\Models\Ent;

use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Uuid;
use Validator;
use Gmf\Sys\Models\User;

class Ent extends Model
{
  use Snapshotable, HasGuard;
  protected $table = 'gmf_sys_ents';
  public $incrementing = false;
  protected $keyType = 'string';
  protected $fillable = [
    'id', 'openid', 'code', 'name', 'discover', 'gateway', 'memo', 'short_name', 'avatar', 'avatar_id',
    'industry', 'area', 'revoked'
  ];
  protected $hidden = ['token'];
  public function formatDefaultValue($attrs)
  {
    if (empty($this->openid)) {
      $this->openid = Uuid::generate();
    }
    if (empty($this->token)) {
      $this->token = Uuid::generate();
    }
    if (empty($this->code)) {
      $this->code = Uuid::generate();
    }
    if (empty($this->avatar)) {
      $this->avatar = '/assets/vendor/gmf-sys/avatar/' . mt_rand(1, 50) . '.jpg';
    }
    if (empty($this->revoked)) {
      $this->revoked = 0;
    }
  }
  public function createToken()
  {
    $this->token = Uuid::generate();
    $this->save();
  }
  public function validate()
  {
    Validator::make($this->toArray(), [
      'code' => ['required'],
      'openid' => ['required'],
    ])->validate();
  }
  public function hasUser($userId)
  {
    return EntUser::where('ent_id', $this->id)->where('user_id', $userId)->exists();
  }
  public static function setEffective($entId, $userId, $is_effective)
  {
    $m = EntUser::where('ent_id', $entId)->where('user_id', $userId)->first();
    if (empty($m)) {
      throw new \Exception('用户或者企业不存在!');
    }
    $m->is_effective = $is_effective ? 1 : 0;
    $m->save();
    return $m;
  }
  public static function addUser($entId, $userId, $type = 'member')
  {
    $options = [];
    if (is_string($type)) {
      $options['type_enum'] = $type;
    } else if (is_array($type)) {
      $options = array_only($type, ['type_enum', 'is_effective', 'token']);
    }
    $m = EntUser::where('ent_id', $entId)->where('user_id', $userId)->first();
    if (!$m) {
      $m = EntUser::updateOrCreate(['ent_id' => $entId, 'user_id' => $userId], $options);
    } else {
      $m->fill($options);
      $m->revoked = 0;
      $m->save();
    }
    return $m;
  }
  public static function findByCode($code, array $opts = [])
  {
    if (empty($code)) {
      return false;
    }

    if (empty($opts)) {
      $opts = [];
    }

    $opts['code'] = $code;
    return static::where($opts)->first();
  }
  public static function build(Closure $callback)
  {
        //id,root,parent,code,name,memo,uri,sequence
    tap(new Builder, function ($builder) use ($callback) {
      $callback($builder);
            //用户验证
      if (!empty($builder->user_id)) {
        $user = User::find($builder->user_id);
        if (!$user) {
          $user = User::create(['type' => 'sys', 'id' => $builder->user_id, 'name' => $builder->name]);
          $builder->user_id($user->id);
        }
      }
      if (empty($builder->user_id)) {
        $user = User::create(['type' => 'sys', 'name' => $builder->name]);
        $builder->user_id($user->id);
      }

            //企业
      $ent = false;

      if (!empty($builder->id)) {
        $ent = Ent::find($builder->id);
        if (!$ent) {
          $ent = static::create(array_only($builder->toArray(), ['id', 'code', 'name', 'memo', 'short_name', 'avatar', 'dc_host', 'industry', 'area']));
        }
      }
      if (!$ent) {
        $ent = static::create(array_only($builder->toArray(), ['id', 'code', 'name', 'memo', 'short_name', 'avatar', 'dc_host', 'industry', 'area']));
      }
      return $ent;
    });
  }
}
