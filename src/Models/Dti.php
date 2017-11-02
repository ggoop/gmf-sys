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
