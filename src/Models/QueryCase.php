<?php
namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class QueryCase extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_query_cases';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'user_id', 'query_id', 'name', 'comment', 'data', 'size'];
	protected $hidden = ['created_at', 'updated_at'];
}
