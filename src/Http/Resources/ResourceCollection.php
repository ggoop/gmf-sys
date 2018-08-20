<?php

namespace Gmf\Sys\Http\Resources;

use Closure;
use Illuminate\Http\Resources\Json\ResourceCollection as BaseResourceCollection;
use Gmf\Sys\Builder;
class ResourceCollection extends BaseResourceCollection
{
  private $itemCallback;

  public function withItemCallback(Closure $itemCallback = null)
  {
    $this->itemCallback = $itemCallback;
    return $this;
  }

  /**
   * Create a new resource instance.
   *
   * @param  mixed  $resource
   * @return void
   */
  public function __construct($resource)
  {
    parent::__construct($resource);
  }
  public function toResult($request)
  {
    $rtn = $this->toArray($request);
    if (!is_null($this->itemCallback)) {
      $rtn = collect($rtn)->map(function ($v) {
        $v = new Builder($v);
        $flag = call_user_func($this->itemCallback, $v, $this);
        if ($flag === 0 || $flag === false) {
          return null;
        }
        return $v;
      })->reject(function ($v) {
        return $v === null;
      })->all();
    }
    return $rtn;
  }
}
