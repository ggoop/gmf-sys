<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfOauthAccessTokensTable extends Migration {
	private $mdID = "9576e8701f5e11e7b059eb2b03229f44";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.oauth.access.token')->comment('令牌')->tableName('gmf_oauth_access_tokens');

		$md->string('id', 100)->primary();
		$md->entity('user', 'gmf.sys.user')->nullable()->comment('用户');
		$md->entity('client', 'gmf.oauth.client')->comment('客户端');
		$md->string('name')->nullable();
		$md->text('scopes')->nullable();
		$md->boolean('revoked');
		$md->dateTime('expires_at')->nullable();
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
