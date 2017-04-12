<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfOauthAuthCodesTable extends Migration {
	private $mdID = "9576e4a01f5e11e7ab6673ace9754dd3";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.oauth.auth.code')->comment('授权码')->tableName('gmf_oauth_auth_codes');

		$md->string('id', 100)->primary();
		$md->entity('user', 'gmf.sys.user')->comment('用户');
		$md->entity('client', 'gmf.oauth.client')->comment('客户端');
		$md->text('scopes')->nullable();
		$md->boolean('revoked');
		$md->dateTime('expires_at')->nullable();

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
