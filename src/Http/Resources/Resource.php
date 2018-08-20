<?php
namespace Gmf\Sys\Http\Resources;

use Closure;
use Gmf\Sys\Builder;
use Illuminate\Http\Resources\Json\Resource as BaseResource;

class Resource extends BaseResource
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
  public static function collection($resource)
  {
    return new AnonymousResourceCollection($resource, get_called_class());
  }
  public function toResult($request)
  {
    $rtn = new Builder($this->toArray($request));
    if (!is_null($this->itemCallback)) {
      $flag = call_user_func($this->itemCallback, $rtn, $this);
      if ($flag === 0 || $flag === false) {
        return false;
      }
    }
    return $rtn;
  }
}
