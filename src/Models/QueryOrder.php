<?php
namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class QueryOrder extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_query_orders';
	protected $fillable = ['query_id', 'name', 'comment', 'direction', 'sequence'];
	protected $hidden = ['created_at', 'updated_at'];
}
