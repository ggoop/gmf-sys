<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysEntStatusEnum extends Migration {
	public $mdID = "94e96440524d11e8be6a5fcd21851dc0";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.ent.status.enum')->comment('企业状态');
		$md->string('draft')->comment('草稿')->default(0);
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
