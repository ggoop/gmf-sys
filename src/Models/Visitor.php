<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model {
	use HasGuard;
	protected $table = 'gmf_md_visitors';
	public $timestamps = false;
	public $dates = ['created_at'];

	protected $fillable = ['created_at',
		'user_id', 'ent_id', 'ip', 'path', 'url', 'method',
		'params', 'query', 'body', 'header',
		'agent', 'referer', 'times', 'actimes',
		'client_name', 'client_sn', 'client_id', 'client_account',
	];
}
