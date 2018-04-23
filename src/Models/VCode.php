<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class VCode extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_vcodes';
	public $incrementing = false;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['id',
		'client_id', 'client_key', 'content',
		'user_id', 'channel', 'type', 'token', 'expire_time',
	];
}
