<?php

namespace Gmf\Sys\Models;
use Closure;
use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Validator;

class DtiCategory extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_dti_categories';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'host', 'is_revoked'];
	protected $casts = [
		'is_revoked' => 'boolean',
	];
	public function params() {
		return $this->hasMany('Gmf\Sys\Models\DtiParam', 'category_id');
	}
	public static function fromImport($datas) {
		return $datas->map(function ($row) {
			$entId = GAuth::entId();
			$data = array_only($row, [
				'code', 'name', 'host',
			]);
			Validator::make($data, [
				'code' => 'required',
				'name' => 'required',
			])->validate();
			return static::updateOrCreate(['ent_id' => $entId, 'code' => $data['code']], $data);
		});
	}

	public static function build(Closure $callback) {
		//id,root,parent,code,name,memo,uri,sequence
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'code', 'name', 'host', 'is_revoked']);

			$find = array_only($data, ['code', 'ent_id']);
			static::updateOrCreate($find, $data);
		});
	}
}
