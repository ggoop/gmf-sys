<?php
namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class QueryField extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_query_fields';
	protected $fillable = ['query_id', 'name', 'comment', 'sequence'];
	protected $hidden = ['created_at', 'updated_at'];
}
