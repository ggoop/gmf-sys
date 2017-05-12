<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Org extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_org_orgs';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'memo', 'shortName', 'avatar'];
	public function ent() {
		return $this->belongsTo('Gmf\Sys\Models\Ent');
	}
}
