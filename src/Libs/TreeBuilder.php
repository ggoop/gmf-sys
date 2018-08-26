<?php

namespace Gmf\Sys\Libs;
use Illuminate\Support\Collection;

class TreeBuilder {
  private $nodes = [];
  public function __construct($nodes) {
    $this->nodes = $nodes;
  }
  public function build() {
    if (is_array($this->nodes)) {
      $nodes = $this->nodes;
    } else if ($this->nodes instanceof Collection) {
      $nodes = $this->nodes->all();
    } else {
      $nodes = $this->nodes;
    }
    foreach ($nodes as $k => $v) {
      if (empty($v)) {
        continue;
      }
      //查询子节点
      $subs = [];
      foreach ($nodes as $sk => $sv) {
        if ($sv && $sv->parent_id == $v->id && $sv->id != $v->id) {
          $subs[] = $sv;
          $nodes[$sk] = null;
        }
      }
      //如果存在子节点
      if (count($subs)) {
        $v->childs = $subs;
      }
    }
    $ns = [];
    foreach ($nodes as $k => $v) {
      if (!empty($v)) {
        $ns[] = $v;
      }
    }
    return $ns;
  }

  public static function create($data) {
    $b = new TreeBuilder($data);
    return $b->build();
  }
}