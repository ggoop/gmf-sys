<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class EntUser extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_ent_users';
	public $incrementing = false;

	protected $keyType = 'string';
	protected $fillable = ['id', 'user_id', 'ent_id', 'type_enum', 'revoked'];
}
