<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Dept extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_org_depts';
	public $incrementing = false;
	protected $keyType = 'string';
	protected $fillable = ['id', 'code', 'name', 'memo', 'ent_id', 'org_id'];

	public function ent() {
		return $this->belongsTo('Gmf\Sys\Models\Ent');
	}
	public function org() {
		return $this->belongsTo('Gmf\Sys\Models\Org');
	}
}
