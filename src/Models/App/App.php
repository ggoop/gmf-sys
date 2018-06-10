<?php

namespace Gmf\Sys\Models\App;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Uuid;
use Gmf\Sys\Database\Concerns\BatchImport;
use Validator;
class App extends Model {
	use Snapshotable, HasGuard,BatchImport;
	protected $table = 'gmf_sys_apps';
	public $incrementing = false;
	protected $keyType = 'string';
	protected $fillable = ['id', 'openid', 'code', 'name', 'memo', 'discover', 'gateway', 'revoked'];
	protected $hidden = ['token'];
	public function formatDefaultValue($attrs) {
		if (empty($this->openid)) {
			$this->openid = Uuid::generate();
		}
		if (empty($this->revoked)) {
			$this->revoked = 0;
		}
	}
	public function validate() {
		Validator::make($this->toArray(), [
			'code' => ['required'],
			'name' => ['required'],
		])->validate();
	}
	public function uniqueQuery($query) {
		$query->where(function($query){
			$query->where('openid',$this->openid)->orWhere('code',$this->code);
		});
	}
	public static function fromImport($datas) {
		$datas = $datas->map(function ($row) {
			return $row;
		});
		static::BatchImport($datas);
	}
}
