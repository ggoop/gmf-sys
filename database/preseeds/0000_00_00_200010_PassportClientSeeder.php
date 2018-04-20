<?php

use Gmf\Sys\Passport\Client;
use Gmf\Sys\Passport\PersonalAccessClient;
use Gmf\Sys\Builder;
use Illuminate\Database\Seeder;

class PassportClientSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		$b = new Builder;
		$id = config('gmf.client.id');
		$name = config('gmf.client.name');
		$secret = config('gmf.client.secret');
		$userId = config('gmf.client.user');
		if (!$id) {
			return;
		}

		//客户端
		$b = new Builder;
		$b->name($name)->secret($secret)->user_id($userId);
		$b->personal_access_client(1)->password_client(1)->revoked(0);
		Client::updateOrCreate(['id' => $id], $b->toArray());

		//私有登录
		$b = new Builder;
		PersonalAccessClient::updateOrCreate(['client_id' => $id], $b->toArray());
	}
}
