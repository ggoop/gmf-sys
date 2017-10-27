<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Lns extends Model {
	use Snapshotable, HasGuard;
	public $timestamps = false;
	protected $table = 'gmf_sys_lns';
	public $incrementing = false;
	protected $fillable = ['id', 'code', 'name',
		'serial_number', 'request_code', 'request_date',
		'fm_date', 'to_date',
		'answer_code'];
}
