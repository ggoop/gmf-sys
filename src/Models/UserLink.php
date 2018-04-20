<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Illuminate\Database\Eloquent\Model;

class UserLink extends Model {
	use HasGuard;
	protected $table = 'gmf_sys_user_links';
	protected $fillable = ['fm_user_id', 'to_user_id', 'is_default'];
}
