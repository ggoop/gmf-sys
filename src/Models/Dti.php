<?php

namespace Gmf\Sys\Models;
use Closure;
use GAuth;
use Gmf\Sys\Builder;
use Gmf\Sys\Libs\InputHelper;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;
use Validator;

class Dti extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_dtis';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'code', 'name', 'memo', 'category_id', 'host', 'path', 'method_enum',
		'local_id',
		'sequence', 'header', 'body', 'query', 'is_running',
		'begin_date', 'end_date', 'msg'];
	protected $casts = [
		'is_running' => 'boolean',
	];

	public function category() {
		return $this->belongsTo('Gmf\Sys\Models\DtiCategory');
	}
	public function local() {
		return $this->belongsTo('Gmf\Sys\Models\DtiLocal');
	}
	public function params() {
		return $this->hasMany('Gmf\Sys\Models\DtiParam', 'dti_id');
	}

	public static function fromImport($datas) {
		$datas->map(function ($row) {
			$entId = GAuth::entId();
			$data = array_only($row, [
				'code', 'name', 'method_enum', 'path', 'header', 'body', 'query',
			]);
			$data = InputHelper::fillEntity($data, $row, [
				'category' => function ($v, $data) use ($entId) {
					return DtiCategory::where('ent_id', $entId)->where(function ($query) use ($v) {
						$query->where('code', $v)->orWhere('name', $v);
					})->value('id');
				},
				'local' => function ($v, $data) {
					return DtiLocal::where('code', $v)->orWhere('name', $v)->value('id');
				},
			]);
			$data = InputHelper::fillEnum($data, $row, [
				'method' => 'gmf.sys.dti.method.enum',
			]);
			Validator::make($data, [
				'code' => 'required',
				'name' => 'required',
				'category_id' => 'required',
				'local_id' => 'required',
			])->validate();
			return static::updateOrCreate(['ent_id' => $entId, 'category_id' => $data['category_id'], 'code' => $data['code']], $data);
		});
	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'code', 'name', 'memo', 'category_id', 'host', 'path', 'method_enum', 'local_id', 'sequence', 'header', 'body', 'query', 'is_running']);

			$category = false;
			if (!empty($builder->category)) {
				$category = DtiCategory::where('code', $builder->category)->where('ent_id', $builder->ent_id)->first();
			}
			if ($category) {
				$data['category_id'] = $category->id;
			}
			$local = false;
			if (!empty($builder->local)) {
				$local = DtiLocal::where('code', $builder->local)->first();
			}
			if ($local) {
				$data['local_id'] = $local->id;
			}

			$find = array_only($data, ['code', 'ent_id']);
			static::updateOrCreate($find, $data);

		});
	}
}
