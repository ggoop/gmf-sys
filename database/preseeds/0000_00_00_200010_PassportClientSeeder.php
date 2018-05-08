<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Passport\Client;
use Gmf\Sys\Passport\PersonalAccessClient;
use Illuminate\Database\Seeder;

class PassportClientSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		$b = new Builder;
		$name = config('gmf.client.name');
		$secret = config('gmf.client.secret');
		$account = config('gmf.client.user');
		if (empty($name) || empty($secret) || empty($account)) {
			return;
		}
		$user = config('gmf.user.model')::findByAccount($account, 'sys');
		if (empty($user)) {
			throw new \Exception("$account is not exsts!");
		}

		//客户端
		$b = new Builder;
		$b->name($name)->secret($secret)->user_id($user->id);
		$b->personal_access_client(1)->password_client(1)->revoked(0);
		$client = Client::updateOrCreate(['name' => $name], $b->toArray());

		//私有登录
		$b = new Builder;
		PersonalAccessClient::updateOrCreate(['client_id' => $client->id], $b->toArray());
	}
}
