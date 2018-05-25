<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysLogLevelEnum extends Migration {
	public $mdID = "01e8595f55cb13e0b56e7738d0b23928";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.log.level.enum')->comment('日志级别');
		$md->string('0')->comment('ALL')->default(12);
		$md->string('14')->comment('DEBUG')->default(14);
		$md->string('16')->comment('INFO')->default(16);
		$md->string('18')->comment('WARN')->default(18);
		$md->string('20')->comment('ERROR')->default(20);
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
