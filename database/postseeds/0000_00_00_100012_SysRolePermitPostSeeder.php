<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models\Authority\RoleMenu;
use Gmf\Sys\Models\Authority\RoleUser;
use Gmf\Sys\Models\Menu;
use Illuminate\Database\Seeder;

class SysRolePermitPostSeeder extends Seeder {
	private $role = 'suite.role.sys.super';
	public $entId = '';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function run() {
		if (empty($this->entId)) {
			$this->entId = config('gmf.ent.id');
		}
		if (empty($this->entId)) {
			return;
		}
		$uid = config('gmf.admin.id');

		RoleUser::build(function (Builder $b) use ($uid) {
			$b->ent_id($this->entId)->user_id($uid)->role($this->role);
		});
		$menus = Menu::where('is_leaf', '1')->get();
		if ($menus && count($menus)) {
			foreach ($menus as $m) {
				RoleMenu::build(function (Builder $b) use ($m) {
					$b->ent_id($this->entId)->menu_id($m->id)->role($this->role)->opinion_enum('permit');
				});
			}
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {

	}
}
