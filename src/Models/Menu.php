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
	protected $fillable = ['id', 'code', 'name', 'memo', 'uri'];
	public function menus() {
		//方法的第一个参数为我们希望最终访问的模型名称，而第二个参数为中间模型的名称。
		//第三个参数为中间模型的外键名称，
		//而第四个参数为最终模型的外键名称，
		//第五个参数则为本地键。
		return $this->hasManyThrough('Gmf\Sys\Models\Menu', 'Gmf\Sys\Models\MenuRelation', 'parent_id', 'id', 'id');
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

			$data = $builder->toArray();
			static::create($data);

			$dataRelation['root_id'] = $builder->id;
			$dataRelation['parent_id'] = $builder->id;
			$dataRelation['menu_id'] = $builder->id;
			$dataRelation['path'] = $builder->id;

			if (!empty($builder->sequence)) {
				$dataRelation['sequence'] = $builder->sequence;
			}
			if (!empty($builder->parent)) {
				$parent = Menu::where('code', $builder->parent)->first();
				if ($parent) {
					$parentRelation = MenuRelation::where('menu_id', $parent->id)->first();
					if ($parentRelation) {
						$dataRelation['root_id'] = $parentRelation->root_id;
						$dataRelation['parent_id'] = $parentRelation->menu_id;
						$dataRelation['path'] = $parentRelation->path . '.' . $dataRelation['path'];
					}
				}
			}
			MenuRelation::create($dataRelation);
		});
	}
}
