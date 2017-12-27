<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysUserAccountsTable extends Migration {
	public $mdID = "416c4790eaaf11e79a6151efe9c2bc8b";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.user.account')->comment('账号')->tableName('gmf_sys_user_accounts');
		$md->bigIncrements('id');
		$md->entity('user', 'gmf.sys.user')->nullable()->comment('用户');
		$md->entity('account', 'gmf.sys.account')->nullable()->comment('账号');

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
