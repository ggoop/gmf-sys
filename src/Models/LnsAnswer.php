<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class LnsAnswer extends Model {
	use Snapshotable, HasGuard;
	public $timestamps = false;
	protected $table = 'gmf_sys_lns_answers';
	public $incrementing = false;
	protected $fillable = ['id', 'code', 'request_serial', 'request_code', 'fm_date', 'to_date'];
}
