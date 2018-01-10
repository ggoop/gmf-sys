<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model {
	use HasGuard;
	protected $table = 'gmf_sys_user_infos';
	protected $fillable = ['id', 'user_id', 'type', 'content'];
}
