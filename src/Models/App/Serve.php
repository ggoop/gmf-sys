<?php

namespace Gmf\Sys\Models\App;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use GAuth;
use Gmf\Sys\Database\Concerns\BatchImport;

use Validator;
class Serve extends Model {
	use Snapshotable, HasGuard,BatchImport;
	protected $table = 'gmf_sys_app_serves';
	public $incrementing = false;
	protected $keyType = 'string';
	protected $fillable = ['id', 'app_id', 'code', 'name', 'memo', 'method', 'path', 'is_public','revoked'];

	public function formatDefaultValue($attrs) {
		if (empty($this->path)) {
			$this->path = '';
		}
		if (empty($this->revoked)) {
			$this->revoked = 0;
		}
		if (empty($this->is_public)) {
			$this->is_public = 1;
		}
		if (empty($this->app_id) && !empty($attrs['app']) && $v = $attrs['app']) {
			$v = !empty($v['code']) ? $v['code'] : !empty($v->code) ? $v = $v->code : is_string($v) ? $v : false;
			$this->app_id = App::where('code', $v)->value('id');
		}
	}
	public function validate() {
		Validator::make($this->toArray(), [
			'ent_id' => ['required'],
			'code' => ['required'],
			'name' => ['required'],
		])->validate();
	}
	public function uniqueQuery($query) {
		$query->where([
			'app_id' => $this->app_id,
			'code' => $this->code,
		]);
	}
	public static function fromImport($datas) {
		$datas = $datas->map(function ($row) {
			return $row;
		});
		static::BatchImport($datas);
	}
}
