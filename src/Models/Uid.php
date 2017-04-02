<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Illuminate\Database\Eloquent\Model;

class Uid extends Model {
	use HasGuard;
	public $timestamps = false;
	protected $table = 'gmf_sys_uids';
	public $incrementing = false;
	protected $fillable = ['id', 'sn'];
}
