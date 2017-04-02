<?php
namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class QueryCase extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_query_cases';
}
