<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model {
	use HasGuard;
	protected $table = 'gmf_sys_visitors';
	public $timestamps = false;
	public $dates = ['created_at'];

	protected $fillable = ['created_at', 'ip', 'path', 'url', 'method', 'params', 'agent', 'referer', 'times', 'actimes'];
}
