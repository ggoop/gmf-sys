<?php
namespace Gmf\Sys\Http\Controllers;

use Auth;
use DB;
use Gmf\Sys\Models;
use Illuminate\Http\Request;

class EntController extends Controller {
	public function index(Request $request) {
		$data = [];
		$query = Models\Ent::where('id', 1);
		$data = $query->get();
		return $this->toJson($data);
	}
	public function getMyEnts(Request $request) {
		$userID = Auth::id();
		$query = DB::table('gmf_sys_ents as l')->join('gmf_sys_ent_users as u', 'l.id', '=', 'u.ent_id');
		$query->addSelect('l.id', 'l.name', 'l.avatar', 'l.dcHost', 'u.type_enum as type');
		$query->where('u.user_id', $userID);

		$datas = $query->get();
		return $this->toJson($datas);
	}
}
