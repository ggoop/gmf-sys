<?php

namespace Gmf\Sys\Bp;

use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Gmf\Sys\Models\Ent\EntUser;
use Validator;

class Ent
{
  public function create($input = [])
  {
    Validator::make($input, [
      'name' => ['required'],
    ])->validate();
    $ent = config('gmf.ent.model')::create($input);

    $userId = GAuth::id();
    if ($userId) {
      EntUser::updateOrCreate(['ent_id' => $entId, 'user_id' => $userId], ['type_enum' => 'creator']);
    }
    return $ent;
  }
  public function parse($id)
  {
    $c = Models\Scode::find($id);
    if (empty($c)) {
      return false;
    }
    return unserialize($c->content);
  }
}