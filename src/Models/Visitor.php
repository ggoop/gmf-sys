<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model {
	use HasGuard;
	protected $connection = 'log';
	protected $table = 'gmf_sys_visitors';
	public $timestamps = false;
	public $dates = ['created_at'];

	protected $fillable = ['created_at',
		'user_id', 'ent_id', 'ip', 'path', 'url', 'method','user_name','app_id','app_name',
		'input', 'query', 'body','trace', 'header', 'content_type',
		'agent', 'referer', 'times', 'actimes',
		'client_name', 'client_sn', 'client_id', 'client_account',
	];
}
