<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class DtiLog extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_dti_logs';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'dti_id', 'date', 'session', 'content', 'memo', 'state_enum'];
	protected $casts = [
		'date' => 'date',
	];

}
