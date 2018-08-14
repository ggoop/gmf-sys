<?php
namespace Gmf\Sys\Http\Controllers\Ent;

use DB;
use GAuth;
use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Http\Resources;
use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Validator;

class EntController extends Controller {
  public function index(Request $request) {
    $size = $request->input('size', 10);
    $query = Models\Ent\Ent::where('revoked', '0');
    if ($v = $request->input('q')) {
      $query->where(function ($query) use ($v) {
        $query->where('name', 'like', '%' . $v . '%')->orWhere('code', 'like', '%' . $v . '%');
      });
    }
    $query->orderBy('name');

    $meJoins = [];
    if (GAuth::ids()) {
      $meJoins = Models\Ent\EntUser::whereIn('user_id', GAuth::ids())->pluck('ent_id')->all();
    }
    return $this->toJson(Resources\Ent::collection($query->paginate($size))->withItemCallback(function ($sb, $send) use ($meJoins) {
      $sb->is_joined(in_array($sb->id, $meJoins));
    }));
  }
  public function show(Request $request, string $id) {
    $query = Models\Ent\Ent::where('id', $id)->orWhere('code', $id);
    $data = $query->first();
    return $this->toJson($data);
  }
  public function store(Request $request) {
    $input = array_only($request->all(), ['code', 'name', 'memo', 'short_name', 'avatar', 'industry', 'area']);
    $validator = Validator::make($input, [
      'code' => [
        'required',
      ],
      'name' => [
        'required',
      ],
    ]);
    if ($validator->fails()) {
      return $this->toError($validator->errors());
    }
    $data = Models\Ent\Ent::create($input);

    Models\Ent\Ent::addUser($data->id, GAuth::userId(), 'create');
    return $this->show($request, $data->id);
  }
  /**
   * PUT/PATCH
   * @param  Request $request [description]
   * @param  [type]  $id      [description]
   * @return [type]           [description]
   */
  public function update(Request $request, $id) {
    $input = $request->only(['code', 'name', 'memo', 'short_name', 'avatar', 'industry', 'area']);
    $validator = Validator::make($input, [
      'code' => [
        'required',
      ],
      'name' => [
        'required',
      ],
    ]);
    if ($validator->fails()) {
      return $this->toError($validator->errors());
    }
    Models\Ent\Ent::where('id', $id)->update($input);
    return $this->show($request, $id);
  }
  public function getToken(Request $request) {
    $ent = Models\Ent\Ent::find(GAuth::entId());
    if (empty($ent)) {
      throw new \Exception('找不到企业！');
    }
    return $this->toJson($ent->token);
  }
  public function createToken(Request $request) {
    $ent = Models\Ent\Ent::find(GAuth::entId());
    if (empty($ent)) {
      throw new \Exception('找不到企业！');
    }
    $ent->createToken();
    return $this->toJson($ent->token);
  }
  public function destroy(Request $request, $id) {
    $ids = explode(",", $id);
    Models\Ent\Ent::destroy($ids);
    return $this->toJson(true);
  }
  public function getMyEnts(Request $request) {
    $size = $request->input('size', 10);
    $userID = GAuth::userId();
    $query = DB::table('gmf_sys_ents as l')->join('gmf_sys_ent_users as u', 'l.id', '=', 'u.ent_id');
    $query->addSelect('l.id', 'l.name', 'l.avatar', 'u.is_default', 'u.type_enum as type');
    $query->where('u.user_id', $userID);
    $query->where('u.is_effective', '1');
    $query->orderBy('u.is_default', 'desc')->orderBy('l.name');
    return $this->toJson($query->paginate($size));
  }
  public function join(Request $request) {
    $entId = $request->input('entId');
    $ent = Models\Ent\Ent::find($entId);
    if (empty($ent)) {
      throw new \Exception('找不到企业！');
    }
    $userId = GAuth::id();
    if (!$ent->hasUser($userId)) {
      Models\Ent\Ent::addUser($ent->id, $userId, ['is_effective' => 0, 'type_enum' => 'member']);
    } else {
      return $this->toJson(false);
    }
    return $this->toJson(true);
  }
  public function setDefault(Request $request) {
    $entId = $request->input('entId');
    $ent = Models\Ent\Ent::find($entId);
    if (empty($ent)) {
      throw new \Exception('找不到企业！');
    }
    $userId = GAuth::id();

    Models\Ent\EntUser::where('user_id', $userId)->where('ent_id', '!=', $ent->id)->update(['is_default' => 0]);
    Models\Ent\EntUser::where('user_id', $userId)->where('ent_id', $ent->id)->update(['is_default' => 1]);
    return $this->toJson(true);
  }
}
