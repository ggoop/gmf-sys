<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class FileContent extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_file_contents';
	public $incrementing = false;
	protected $fillable = ['id', 'file_id', 'data'];

}
