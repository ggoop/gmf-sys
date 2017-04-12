<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAccountsTable extends Migration {
	private $mdID = "5c8aad701f4411e7b47819d02749b2be";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.account')->comment('账号')->tableName('gmf_sys_accounts');
		$md->string('id', 100)->primary();
		$md->string('name')->nullable()->comment('账号名');
		$md->string('nickName')->nullable()->comment('显示名称');
		$md->string('type')->nullable()->comment('类型');
		$md->string('secret', 100)->nullable();
		$md->text('redirect')->nullable();
		$md->string('token')->nullable()->comment('token');

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
