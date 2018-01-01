<?php

namespace Gmf\Sys\Models;
use Gmf\Sys\Services\File as FileSv;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class File extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_files';
	public $incrementing = false;
	protected $fillable = [
		'id', 'ent_id', 'user_id',
		'disk', 'code', 'type', 'title',
		'ext', 'url', 'path', 'size', 'props', 'is_revoked'];
	public static function storage(Request $request, $mdFiles, $path = '', $disk = 'public') {
		$sv = new FileSv($request);
		return $sv->storage($mdFiles, $path, $disk);
	}
	public function user() {
		return $this->belongsTo(config('gmf.user.model'));
	}
}
