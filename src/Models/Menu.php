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
	protected $fillable = ['id', 'root_id', 'parent_id', 'is_leaf',
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
				$parent = static::where(function ($query) use ($builder) {
					$query->where('code', $builder->parent)->orWhere('id', $builder->parent);
				})->first();
			}
			if ($parent) {
				$data['root_id'] = $parent->root_id;
				$data['parent_id'] = $parent->id;
			} else {
				$data['root_id'] = $builder->id;
				$data['parent_id'] = null;
			}

			$find = static::where(function ($query) use ($builder) {
				$query->where('code', $builder->code)->orWhere('id', $builder->id);
			})->first();
			if ($find) {
				static::where('id', $find->id)->update($data);
			} else {
				$find = static::create($data);
			}
			if ($parent) {
				static::where('id', $parent->id)->update(['is_leaf' => '0']);
			}
		});
	}
}
