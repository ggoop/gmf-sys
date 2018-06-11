<?php

namespace Gmf\Sys\Models\App;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class AppUser extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_app_users';
	protected $fillable = ['app_id', 'user_id', 'is_default', 'type_enum','revoked'];
}
