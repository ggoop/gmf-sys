<?php

namespace Gmf\Sys\Models\App;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class EntApp extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_ent_apps';
	protected $fillable = ['ent_id', 'app_id', 'token', 'discover','gateway','is_default'];
}
