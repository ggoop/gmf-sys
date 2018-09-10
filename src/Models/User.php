<?php

namespace Gmf\Sys\Models;

use Gmf\Sys\Passport\HasApiTokens;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Uuid;
use Validator;

class User extends Authenticatable
{
  use Snapshotable, HasGuard, HasApiTokens, Notifiable;

  protected $table = 'gmf_sys_users';
  public $incrementing = false;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id', 'openid', 'account', 'mobile', 'email', 'password', 'token',
    'name', 'nick_name', 'gender',
    'type', 'cover', 'cover_id', 'avatar', 'avatar_id', 'titles', 'memo', 'status_enum',
    'client_id', 'client_type', 'client_name', 'src_id', 'src_url', 'info',
  ];

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token', 'token',
  ];
  public function avatar2()
  {
    return $this->belongsTo('Gmf\Sys\Models\File', 'avatar_id');
  }
  public function formatDefaultValue($attrs)
  {
    if (empty($this->openid)) {
      $this->openid = Uuid::generate();
    }
    if (empty($this->token)) {
      $this->token = Uuid::generate();
    }
  }
  public function validate()
  {
    Validator::make($this->toArray(), [
      'type' => ['required'],
      'openid' => ['required'],
    ])->validate();
  }

  public function findForPassport($username)
  {
    return User::where('account', $username)->first();
  }
  public function findEntIds($type = ['sys', 'web'])
  {
    $query = Ent\EntUser::whereIn('user_id', $this->findLinkUserIds())->where('is_effective', '1');
    $query->where('revoked', 0);

    $query = Ent\Ent::whereIn('id', $query->pluck('ent_id')->all())->where('revoked', 0);
    return $query->pluck('id')->all();
  }
  public function findLinkUserIds($type = ['sys', 'web'])
  {
    $links = collect([$this->id]);
    $links = $links->merge(UserLink::where('fm_user_id', $this->id)->pluck('to_user_id')->all());

    $links = $links->merge(UserLink::where('to_user_id', $this->id)->pluck('fm_user_id')->all());

    $query = User::whereIn('id', $links->unique()->reject(function ($v) {
      return empty($v);
    })->values()->all());
    if (is_array($type)) {
      $query->whereIn('type', $type);
    }
    if (is_string($type) && $type != 'all') {
      $query->where('type', $type);
    }
    return $query->pluck('id')->all();
  }
  public function routeNotificationForNexmo()
  {
    return $this->mobile;
  }
  public function routeNotificationForMail()
  {
    return $this->email;
  }
  public function validateForPassportPasswordGrant($password)
  {
    //var_dump($password);
    return true;
  }
  public static function findByAccount($account, $type, array $opts = [])
  {
    if (empty($account) || empty($type)) {
      return false;
    }

    if (empty($opts)) {
      $opts = [];
    }

    $opts['account'] = $account;
    $opts['type'] = $type;
    return static::where($opts)->first();
  }
  public static function registerByAccount($type, array $opts = [])
  {
    $user = false;
    $opts['type'] = $type;

    Validator::make($opts, [
      'name' => 'required',
    ])->validate();
    if ($type != 'sys') {
      Validator::make($opts, [
        'client_id' => 'required',
      ])->validate();
    }

    if (empty($opts['account']) && empty($opts['email']) && empty($opts['mobile']) && empty($opts['src_id'])) {
      throw new \Exception('account,email ,mobile,src_id not empty least one');
    }
    if (empty($opts['mobile']) && !empty($opts['account'])) {
      if (Validator::make($opts, ['account' => 'required|digits:11'])->passes()) {
        $opts['mobile'] = $opts['account'];
      }
    }
    if (empty($opts['email']) && !empty($opts['account'])) {
      if (Validator::make($opts, ['account' => 'required|email'])->passes()) {
        $opts['email'] = $opts['account'];
      }
    }
    if (empty($opts['name']) && !empty($opts['account'])) {
      $opts['name'] = $opts['account'];
    }
    if (empty($opts['nick_name']) && !empty($opts['name'])) {
      $opts['nick_name'] = $opts['name'];
    }
    if (empty($opts['avatar'])) {
      $opts['avatar'] = '/assets/vendor/gmf-sys/avatar/' . mt_rand(1, 50) . '.jpg';
    }
    if (empty($opts['account'])) {
      $opts['account'] = Uuid::generate();
    }
    if (empty($opts['token'])) {
      $opts['token'] = Uuid::generate();
    }
    $query = static::where('type', $type);
    if (!in_array($opts['type'], ['sys', 'web'])) {
      $query->where('client_id', $opts['client_id']);
    }
    if (!empty($opts['openid'])) {
      $query->where('openid', $opts['openid']);
    } else if (!empty($opts['src_id']) && !in_array($opts['type'], ['sys', 'web'])) {
      $query->where('src_id', $opts['src_id']);
    } else {
      $query->where(function ($query) use ($opts) {
        $f = false;
        if (!empty($opts['account'])) {
          $f = true;
          $query->orWhere('mobile', $opts['account'])
            ->orWhere('email', $opts['account'])
            ->orWhere('account', $opts['account']);
        }
        if (!empty($opts['mobile'])) {
          $f = true;
          $query->orWhere('mobile', $opts['mobile']);
        }
        if (!empty($opts['email'])) {
          $f = true;
          $query->orWhere('email', $opts['email']);
        }
        if (!$f) {
          $query->where('id', 'xx==xx');
        }
      });
    }

    $user = $query->orderBy('created_at', 'desc')->first();

    $data = array_only($opts, [
      'id', 'openid', 'account', 'mobile', 'email', 'password', 'token',
      'name', 'nick_name', 'gender',
      'type', 'cover', 'cover_id', 'avatar', 'avatar_id', 'titles', 'memo',
      'client_id', 'client_type', 'client_name', 'src_id', 'src_url', 'info', 'status_enum',
    ]);
    if ($user) {
      $updates = [];
      if (empty($user->mobile) && !empty($data['mobile']) && !in_array($type, ['sys', 'web'])) {
        $updates['mobile'] = $data['mobile'];
      }
      if (empty($user->email) && !empty($data['email']) && !in_array($type, ['sys', 'web'])) {
        $updates['email'] = $data['email'];
      }
      if (empty($user->gender) && !empty($data['gender'])) {
        $updates['gender'] = $data['gender'];
      }
      if (empty($user->titles) && !empty($data['titles'])) {
        $updates['titles'] = $data['titles'];
      }
      if (empty($user->memo) && !empty($data['memo'])) {
        $updates['memo'] = $data['memo'];
      }
      if (count($updates) > 0) {
        User::where('id', $user->id)->update($updates);
      }
    } else {
      if (!empty($data['password']) && in_array($type, ['sys', 'web'])) {
        $data['password'] = bcrypt($data['password']);
      } else {
        unset($data['password']);
      }
      $user = User::create($data);
    }
    return $user;
  }
  public function resetPassword($pw)
  {
    $this->password = bcrypt($pw);
    $this->save();
  }
  public function linkUser($user)
  {
    if ($this->account == $user->account) {
      return false;
    }
    $credentials = ['fm_user_id' => $this->id, 'to_user_id' => $user->id];
    UserLink::updateOrCreate($credentials);
    return true;
  }
}
