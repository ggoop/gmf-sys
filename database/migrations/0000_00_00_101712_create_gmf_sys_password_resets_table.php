<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysPasswordResetsTable extends Migration {
	public $mdID = "416c49a0eaaf11e7a0300b456d60b448";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.password.reset')->comment('密码重置')->tableName('gmf_sys_password_resets');

		$md->string('email')->index();
		$md->string('token')->index();
		$md->timestamp('created_at')->nullable();

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
