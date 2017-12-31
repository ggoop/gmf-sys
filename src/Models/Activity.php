<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_activities';
	public $incrementing = false;
	protected $fillable = ['id', 'type', 'causer_id', 'causer_type', 'user_id', 'content'];
	public function user() {
		return $this->belongsTo(config('gmf.user.model'));
	}
	public function causer() {
		return $this->morphTo();
	}
}
