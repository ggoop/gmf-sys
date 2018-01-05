<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Illuminate\Database\Eloquent\Model;

class Scode extends Model {
	use HasGuard;
	protected $table = 'gmf_sys_scodes';
	public $incrementing = false;
	protected $fillable = [
		'id', 'client_id', 'client_key',
		'user_id', 'type', 'code', 'content', 'expire_time'];

}
