<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysUserAccountsTable extends Migration {
	private $mdID = "5c8ab2b01f4411e7ade60f16498604d2";
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
