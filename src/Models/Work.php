<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Work extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_org_works';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'memo', 'org_id', 'dept_id'];

	public function ent() {
		return $this->belongsTo('Gmf\Sys\Models\Ent');
	}

	public function org() {
		return $this->belongsTo('Gmf\Sys\Models\Org');
	}

	public function dept() {
		return $this->belongsTo('Gmf\Sys\Models\Dept');
	}
}
