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
		if (empty($id) || empty($email) || empty($secret)) {
			return;
		}
		//用户
		$b = new Builder;
		$b->user_id($id)->account($email)->name($email)->password($secret)->client_id(config('gmf.client.id'));
		User::registerByAccount('sys', $b->toArray());
	}
}
