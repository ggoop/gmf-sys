<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class LnsRequest extends Model {
	use Snapshotable, HasGuard;
	public $timestamps = false;
	protected $table = 'gmf_sys_lns_requests';
	public $incrementing = false;
	protected $fillable = ['id', 'code', 'serial', 'fm_date', 'to_date', 'content'];
}
