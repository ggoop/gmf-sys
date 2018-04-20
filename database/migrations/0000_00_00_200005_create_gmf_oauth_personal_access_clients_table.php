<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfOauthPersonalAccessClientsTable extends Migration {
	public $mdID = "9576eb601f5e11e7898ff597a061de02";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.oauth.personal.access.client')->comment('授权码')->tableName('gmf_oauth_personal_access_clients');

		$md->string('id', 100)->primary();
		$md->entity('client', 'gmf.oauth.client')->comment('客户端');
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
