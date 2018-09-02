<?php

namespace Gmf\Sys\Http\Resources;
use Closure;
use Gmf\Sys\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection {
  private $callback;

  public function withCallback(Closure $callback = null) {
    $this->callback = $callback;
    return $this;
  }
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request
   * @return array
   */
  public function toArray($request) {
    $rtn = [];
    foreach ($this->collection as $key => $value) {
      $v = $this->formatItem($value);
      if ($v) {
        $rtn[] = $v;
      }
    }
    return $rtn;
  }
  private function formatItem($value) {
    $rtn = new Builder;
    $rtn->id($value->id)
      ->name($value->name)
      ->nick_name($value->nick_name)
      ->avatar($value->avatar)
      ->cover($value->cover)
      ->titles($value->titles)
      ->memo($value->memo);

    if (!empty($this->avatar_id)) {
      $rtn->avatar_url(url('/api/sys/images/' . $this->avatar_id));
    } else if (!empty($this->avatar)) {
      $rtn->avatar_url($this->avatar);
    }
    if (empty($rtn->nick_name)) {
      $rtn->nick_name($rtn->name);
    }
    if (!is_null($this->callback)) {
      $flag = call_user_func($this->callback, $rtn, $value);
      if ($flag === 0) {
        return false;
      }
    }
    return $rtn;
  }
}
