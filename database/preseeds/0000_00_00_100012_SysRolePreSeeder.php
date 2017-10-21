<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models\Authority\Role;
use Illuminate\Database\Seeder;

class SysRolePreSeeder extends Seeder {
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
		Role::build(function (Builder $b) {
			$b->ent_id($this->entId)->code($this->role)->name('超级管理员');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Role::where('code', $this->role)->delete();
	}
}
