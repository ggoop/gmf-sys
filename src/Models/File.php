<?php

namespace Gmf\Sys\Models;

use Gmf\Sys\Services\File as FileSv;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Storage;

class File extends Model
{
  use Snapshotable, HasGuard;
  protected $table = 'gmf_sys_files';
  public $incrementing = false;
  protected $fillable = [
    'id', 'ent_id', 'user_id',
    'disk', 'code', 'type', 'title', 'pdf_need',
    'ext', 'url', 'path', 'size', 'props', 'revoked'
  ];

  protected $appends = ['local_path'];
  public static function storage(Request $request = null, $mdFiles, $path = '', $disk = 'public')
  {
    $sv = new FileSv($request);
    return $sv->storage($mdFiles, $path, $disk);
  }
  public function user()
  {
    return $this->belongsTo(config('gmf.user.model'));
  }
  public function getLocalPathAttribute($value)
  {
    return str_replace("/", "\\", Storage::disk($this->disk)->path($this->path));
  }
}
