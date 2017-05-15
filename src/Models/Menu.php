<?php

namespace Gmf\Sys\Models;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_menus';
	public $incrementing = false;
	protected $fillable = ['id', 'root_id', 'parent_id',
		'code', 'name', 'memo', 'uri', 'icon', 'style', 'tag', 'params',
		'sequence'];
	public function menus() {
		return $this->hasMany('Gmf\Sys\Models\Menu', 'parent_id', 'id');
	}
	/**
	 * add new model data
	 * @param  Closure $callback [description]
	 * @return [type]            [description]
	 */
	public static function build(Closure $callback) {
		//id,root,parent,code,name,memo,uri,sequence
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);

			$data = array_only($builder->toArray(), ['id', 'code', 'name', 'memo', 'uri', 'icon', 'style', 'tag', 'params', 'sequence']);
			$parent = false;
			if (!empty($builder->parent)) {
				$parent = Menu::where('code', $builder->parent)->where('tag', $builder->tag)->first();
			}
			if ($parent) {
				$data['root_id'] = $parent->root_id;
				$data['parent_id'] = $parent->id;
			} else {
				$data['root_id'] = $builder->id;
				$data['parent_id'] = null;
			}
			static::create($data);
		});
	}
}
