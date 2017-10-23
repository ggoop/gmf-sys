<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class EntLns extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_ent_lns';
	public $incrementing = false;

	protected $keyType = 'string';
	protected $fillable = ['id', 'lns_id', 'ent_id', 'revoked'];
}
