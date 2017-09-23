<?php
namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class QueryWhere extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_query_wheres';
	protected $fillable = ['query_id', 'name', 'comment', 'sequence', 'operator_enum', 'value', 'type_enum', 'hide'];
	protected $hidden = ['created_at', 'updated_at'];
}
