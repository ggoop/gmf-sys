<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models\User;
use Illuminate\Database\Seeder;

class SysUserSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		$b = new Builder;
		$id = config('gmf.admin.id');
		$email = config('gmf.admin.email');
		$secret = config('gmf.admin.pwd');
		if (!$id) {
			return;
		}
		//用户
		$b = new Builder;
		$b->user_id($id)->email($email)->name($email)->password($secret);
		User::registerByAccount('sys', $b->toArray());
	}
}
