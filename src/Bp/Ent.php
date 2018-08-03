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
    if ($userId && $ent) {
      EntUser::updateOrCreate(['ent_id' => $ent->id, 'user_id' => $userId], ['type_enum' => 'creator']);
    }
    return $ent;
  }
  public function update($id, $input = [])
  {
    $input = array_only($array, ['name', 'short_name', 'avatar', 'memo', 'discover', 'gateway', 'industry', 'area']);
    $ent = config('gmf.ent.model')::find($id);
    if ($ent) {
      $ent->fill($input);
      $ent->save();
    }
    return $ent;
  }
  public function delete($id)
  {
    $ent = config('gmf.ent.model')::find($id);
    $ent->revoked = 1;
    $ent->save();
    EntUser::where('ent_id', $id)->update(['revoked' => '1']);
    return true;
  }
}