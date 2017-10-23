<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysLnsTable extends Migration {
	public $mdID = "8118fdb00a9d11e7959eebbed0dc7db2";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.lns')->comment('许可')->tableName('gmf_sys_lns');

		$md->string('id')->primary();
		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->nullable()->comment('名称');
		$md->string('serial_number')->nullable()->comment('序列号');
		$md->string('request_code')->nullable()->comment('申请码');
		$md->string('request_date')->nullable()->comment('申请时间');
		$md->string('answer_code')->nullable()->comment('答应码');
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
