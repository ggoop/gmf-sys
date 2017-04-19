<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysUsersTable extends Migration {
	private $mdID = "c8e8486009cb11e7b0899dc169137f43";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.user')->comment('用户')->tableName('gmf_sys_users');
		$md->string('id', 100)->primary();
		$md->string('name')->nullable();
		$md->string('email')->nullable();

		$md->string('type')->nullable()->comment('类型');
		$md->string('avatar', 500)->nullable();
		$md->string('mobile', 20)->nullable();

		$md->string('password')->nullable();

		$md->enum('status', 'gmf.sys.user.statusEnum')->nullable();
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
