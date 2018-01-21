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

class DtiParam extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_dti_params';
	public $incrementing = false;
	protected $fillable = ['id', 'ent_id', 'category_id', 'dti_id', 'code', 'name', 'type_enum', 'value', 'is_revoked'];
	protected $casts = [
		'is_revoked' => 'boolean',
	];
	public function category() {
		return $this->belongsTo('Gmf\Sys\Models\DtiCategory');
	}
	public function dti() {
		return $this->belongsTo('Gmf\Sys\Models\Dti');
	}

	public static function fromImport($datas) {
		return $datas->map(function ($row) {
			$entId = GAuth::entId();
			$data = array_only($row, [
				'code', 'name', 'type_enum', 'value', 'category', 'dti',
			]);

			$data = InputHelper::fillEntity($data, $row, [
				'category' => function ($v, $data) use ($entId) {
					return DtiCategory::where('ent_id', $entId)->where(function ($query) use ($v) {
						$query->where('code', $v)->orWhere('name', $v);
					})->value('id');
				},
				'dti' => function ($v, $data) use ($entId) {
					return Dti::where('ent_id', $entId)->where(function ($query) use ($v) {
						$query->where('code', $v)->orWhere('name', $v);
					})->where('category_id', $data['category_id'])->value('id');
				},
			]);
			$data = InputHelper::fillEnum($data, $row, [
				'type' => 'gmf.sys.dti.param.type.enum',
			]);
			Validator::make($data, [
				'code' => 'required',
				'name' => 'required',
				'type_enum' => 'required',
			])->validate();
			return static::updateOrCreate(['ent_id' => $entId, 'code' => $data['code']], $data);
		});

	}

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'ent_id', 'category_id', 'dti_id', 'code', 'name', 'type_enum', 'value', 'is_revoked']);
			$ent_id = '';
			if (!empty($builder->ent_id)) {
				$ent_id = $builder->ent_id;
			}
			if (!empty($builder->category)) {
				$t = DtiCategory::where('code', $builder->category)->where('ent_id', $ent_id)->first();
				if ($t) {
					$data['category_id'] = $t->id;
				}
			}
			if (!empty($builder->dti)) {
				$t = Dti::where('code', $builder->dti)->where('ent_id', $ent_id)->first();
				if ($t) {
					$data['dti_id'] = $t->id;
				}
			}
			$find = array_only($data, ['code', 'ent_id']);
			static::updateOrCreate($find, $data);
		});
	}
}
