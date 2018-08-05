<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class GroupCategory extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_group_categories';
	public $incrementing = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['id','user_id','ent_id','group_id','root_id','parent_id','code','name','memo','is_system','revoked'];
	public function group() {
		return $this->belongsTo('Gmf\Sys\Models\GroupItem');
	}
	public function root() {
		return $this->belongsTo('Gmf\Sys\Models\GroupCategory');
	}
	public function parent() {
		return $this->belongsTo('Gmf\Sys\Models\GroupCategory');
	}
}
