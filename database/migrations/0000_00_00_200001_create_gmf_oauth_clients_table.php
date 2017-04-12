<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfOauthClientsTable extends Migration {
	private $mdID = "9576ea901f5e11e7b0da0fb9ddcc05d5";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.oauth.client')->comment('客户端')->tableName('gmf_oauth_clients');

		$md->string('id', 100)->primary();
		$md->entity('user', 'gmf.sys.user')->comment('用户');

		$md->string('name');
		$md->string('secret', 100);
		$md->text('redirect');
		$md->boolean('personal_access_client');
		$md->boolean('password_client');
		$md->boolean('revoked');
		$md->timestamps();
		$md->build();

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Metadata::dropIfExists($this->mdID);
	}
}
