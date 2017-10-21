<?php

namespace Gmf\Sys\Models\Authority;
use Closure;
use Gmf\Sys\Builder;
use Gmf\Sys\Models\Menu;
use Gmf\Sys\Traits\HasGuard;
use Gmf\Sys\Traits\Snapshotable;
use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model {
	use Snapshotable, HasGuard;
	protected $table = 'gmf_sys_authority_role_menus';
	public $incrementing = false;
	protected $fillable = ['ent_id', 'role_id', 'menu_id', 'opinion_enum'];
	public function role() {
		return $this->belongsTo('Gmf\Sys\Models\Authority\Role');
	}
	public function menu() {
		return $this->belongsTo('Gmf\Sys\Models\Menu');
	}
	public static function build(Closure $callback) {
		tap(new Builder, function ($builder) use ($callback) {
			$callback($builder);
			$data = array_only($builder->toArray(), ['id', 'ent_id', 'role_id', 'menu_id', 'opinion_enum']);

			if (!empty($builder->role)) {
				$tmp = Role::where('code', $builder->role)->where('ent_id', $builder->ent_id)->first();
				$data['role_id'] = $tmp ? $tmp->id : '';
			}
			if (!empty($builder->entity)) {
				$tmp = Menu::where('name', $builder->entity)->first();
				$data['menu_id'] = $tmp ? $tmp->id : '';
			}
			static::updateOrCreate(['ent_id' => $data['ent_id'], 'role_id' => $data['role_id'], 'menu_id' => $data['menu_id']], $data);
		});
	}
}
