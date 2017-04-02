<?php

namespace Gmf\Sys\Models;

use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Component extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_components';
	public $incrementing = false;
	protected $keyType = 'string';
}
