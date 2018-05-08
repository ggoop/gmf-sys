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
		$account = config('gmf.admin.account');
		$secret = config('gmf.admin.pwd');
		if (empty($account) || empty($secret)) {
			return;
		}
		//用户
		$b = new Builder;
		$b->account($account)->name($account)->password($secret)->client_id(config('gmf.client.id'));
		User::registerByAccount('sys', $b->toArray());
	}
}
