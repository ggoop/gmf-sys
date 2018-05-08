<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAppStatusEnum extends Migration {
	public $mdID = "94e966c0524d11e8a680ddcc74b7ade3";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.app.status.enum')->comment('应用状态');
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
