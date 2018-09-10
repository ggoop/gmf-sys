<?php

namespace Gmf\Sys\Http\Resources;

use Closure;
use GAuth;
use Gmf\Sys\Builder;
use Illuminate\Http\Resources\Json\Resource;

class User extends Resource
{
  private $callback;

  public function withCallback(Closure $callback = null)
  {
    $this->callback = $callback;
    return $this;
  }
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request
   * @return array
   */
  public function toArray($request)
  {
    if (empty($this->id)) {
      return false;
    }
    $rtn = new Builder;
    Common::toField($this, $rtn, ['id', 'openid', 'name', 'nick_name', 'gender', 'avatar', 'cover', 'type', 'titles', 'memo']);

    Common::toBooleanField($this, $rtn, ['email_verified', 'mobile_verified']);
    if ($this->id == GAuth::id()) {
      $rtn['is_me'] = true;
      Common::toField($this, $rtn, ['mobile', 'email', 'account']);
    } else {
      $rtn['is_me'] = false;
      if (!empty($this->mobile)) {
        $rtn['mobile'] = $this->hidePhone($this->mobile);
      }
      if (!empty($this->email)) {
        $rtn['email'] = $this->hideEmail($this->email);
      }
      if (!empty($this->account)) {
        $rtn['account'] = $this->hideAccount($this->account);
      }
    }
    if (!empty($this->email_verified) || !empty($this->mobile_verified)) {
      $rtn['verified'] = true;
    } else {
      $rtn['verified'] = false;
    }
    if (!empty($this->pivot)) {
      $rtn['created_at'] = $this->pivot->created_at . '';
    }
    if (empty($rtn['nick_name'])) {
      $rtn['nick_name'] = $rtn['name'];
    }
    if (!is_null($this->callback)) {
      $flag = call_user_func($this->callback, $rtn, $this);
      if ($flag === 0) {
        return false;
      }
    }
    if (!empty($this->avatar_id)) {
      $rtn['avatar_url'] = url('/api/sys/images/' . $this->avatar_id);
    } else if (!empty($this->avatar)) {
      $rtn['avatar_url'] = url($this->avatar);
    }
    return $rtn;
  }
  private function hideEmail($input)
  {
    if (!$input || !$this->isEmail($input)) {
      return $input;
    }
    $array = explode('@', $input);
    if (count($array) < 2) {
      return '***@***';
    }
    if (strlen($array[0]) > 5) {
      $array[0] = str_pad(substr($array[0], 0, 4), strlen($array[0]), '*');
    } else if (strlen($array[0]) > 2) {
      $array[0] = str_pad(substr($array[0], 0, 2), strlen($array[0]), '*');
    } else {
      $array[0] = str_pad('', strlen($array[0]), '*');
    }
    return implode('@', $array);
  }
  private function hideAccount($input)
  {
    if ($this->isEMail($input)) {
      return $this->hideEmail($input);
    } else if ($this->isPhone($input)) {
      return $this->hidePhone($input);
    } else {
      if (strlen($input) > 5) {
        return str_pad(substr($input, 0, 4), strlen($input), '*');
      }
      return str_pad('', strlen($input), '*');
    }
  }
  private function hidePhone($input)
  {
    if (!$input || !$this->isPhone($input)) {
      return $input;
    }
    return substr($input, 0, 3) . '****' . substr($input, 7);
  }
  private function isPhone($number)
  {
    return preg_match("/^1[34578]{1}\d{9}$/", $number);
  }
  private function isEmail($number)
  {
    return filter_var($number, FILTER_VALIDATE_EMAIL);
  }
  public static function anony()
  {
    return [
      'id' => '',
      'name' => '匿名',
      'nick_name' => '匿名',
      'memo' => '',
      'avatar' => '/assets/vendor/gmf-sys/avatar/' . rand(1, 10) . '.jpg',
      'cover' => '/assets/vendor/gmf-sys/cover/1.jpg',
    ];
  }
}
