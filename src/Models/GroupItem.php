<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class GroupItem extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_group_items';
	public $incrementing = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable =['id','user_id','ent_id','code','name','memo','is_system','revoked'];
}
