<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysUsersTable extends Migration {
	public $mdID = "416c3df0eaaf11e7ba254bcf2b38585d";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.user')->comment('用户')->tableName('gmf_sys_users');
		$md->string('id', 100)->primary();

		$md->string('mobile', 20)->nullable();
		$md->string('email')->nullable();
		$md->string('account')->nullable();
		$md->string('password')->nullable();
		$md->string('secret')->nullable();

		$md->string('name')->nullable();
		$md->string('nick_name')->nullable();

		$md->string('type')->nullable()->comment('类型');
		$md->string('avatar', 500)->nullable();
		$md->string('cover', 500)->nullable();
		$md->string('memo', 500)->nullable()->comment('备注');
		$md->enum('status', 'gmf.sys.user.status.enum')->nullable();
		$md->rememberToken();
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
