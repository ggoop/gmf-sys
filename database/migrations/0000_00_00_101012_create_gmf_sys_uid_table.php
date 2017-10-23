<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysUidTable extends Migration {
	public $mdID = "c8e8446009cb11e7b507417db1b03575";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.uid')->comment('UID')->tableName('gmf_sys_uids');
		$md->string('id')->primary();
		$md->bigInteger('sn')->default(0)->index();
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
