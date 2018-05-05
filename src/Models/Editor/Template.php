<?php

namespace Gmf\Sys\Models\Editor;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Template extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_editor_templates';
	public $incrementing = false;
	protected $keyType = 'string';
	protected $fillable = ['id', 'user_id', 'code', 'title', 'memo', 'content', 'revoked'];

	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			$find = [];
			$data = array_only($builder->toArray(), ['id', 'user_id', 'code', 'title', 'memo', 'content']);
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
