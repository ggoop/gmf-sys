<?php

namespace Gmf\Sys\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Dti extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_dtis';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'tag', 'host', 'path', 'method', 'sequence', 'params', 'is_running'];

	public static function run($code, $opts = []) {
		$v = '';
		$query = Dti::where('code', $code);
		if (!empty($opts['ent_id'])) {
			$query->where('ent_id', $opts['ent_id']);
		}
		$p = $query->first();

		return true;
	}
	public static function build(Closure $callback) {
		//id,root,parent,code,name,memo,uri,sequence
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			$data = $builder->toArray();
			static::create(array_only($data, ['id', 'ent_id', 'code', 'name', 'tag', 'host', 'path', 'method', 'sequence', 'params', 'is_running']));

		});
	}
}
