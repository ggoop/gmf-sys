<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysUsersStatusEnum extends Migration {
	public $mdID = "0aad5aa0eaaf11e7a97ef3e78a15340b";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.user.status.enum')->comment('用户状态');
		$md->string('normal')->comment('正常')->default(0);
		$md->string('locked')->comment('锁定')->default(1);
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
