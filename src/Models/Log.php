<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Illuminate\Database\Eloquent\Model;

class Log extends Model {
	use HasGuard;
	protected $connection = 'log';
	protected $table = 'gmf_sys_logs';
	protected $fillable = ['user_id', 'ent_id', 'app_id', 'level_enum',
		'object', 'server', 'client', 'input', 'content'];

}
