<?php
namespace Gmf\Sys\Http\Resources;

use Gmf\Sys\Builder;

class Ent extends Resource {

  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request
   * @return array
   */
  public function toArray($request) {
    if (empty($this->id)) {
      return false;
    }
    $rtn = new Builder;
    Common::toField($this, $rtn, [
      'id', 'code', 'name', 'memo', 'short_name', 'avatar', 'scope',
      'industry', 'area', 'created_at',
    ]);
    return $rtn;
  }
}
