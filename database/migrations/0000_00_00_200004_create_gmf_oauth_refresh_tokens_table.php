<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfOauthRefreshTokensTable extends Migration {
	private $mdID = "9576e9b01f5e11e782a01546970eb634";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.oauth.refresh.token')->comment('更新令牌')->tableName('gmf_oauth_refresh_tokens');

		$md->string('id', 100)->primary();
		$md->string('access_token_id', 100)->index();
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
