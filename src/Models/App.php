<?php

namespace Gmf\Sys\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class App extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_apps';
	public $incrementing = false;
	protected $keyType = 'string';
	protected $fillable = ['id', 'openid', 'code', 'name', 'memo', 'discover', 'gateway', 'revoked'];
	protected $hidden = ['token'];
	public static function build(Closure $callback) {
		//id,root,parent,code,name,memo,uri,sequence
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			$find = [];
			if (!empty($data['id'])) {
				$find['id'] = $data['id'];
			}
			if (!empty($data['code'])) {
				$find['code'] = $data['code'];
			}
			static::updateOrCreate($find, $data);
		});
	}
}
