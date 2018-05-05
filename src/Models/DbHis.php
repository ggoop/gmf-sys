<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Illuminate\Database\Eloquent\Model;

class DbHis extends Model {
	use HasGuard;
	protected $connection = 'log';
	protected $table = 'gmf_sys_dbhis';
}
