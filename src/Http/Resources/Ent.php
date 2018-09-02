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
      'id', 'code', 'name', 'memo', 'short_name', 'avatar', 'avatar_id', 'scope',
      'industry', 'area', 'created_at',
    ]);
    if (!empty($this->avatar_id)) {
      $rtn['avatar_url'] = url('/api/sys/images/' . $this->avatar_id);
    } else if (!empty($this->avatar)) {
      $rtn['avatar_url'] = $this->avatar;
    }
    return $rtn;
  }
}
