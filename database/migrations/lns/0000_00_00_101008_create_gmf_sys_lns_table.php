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
		$md->text('request_serial')->nullable()->comment('序列号');
		$md->text('request_code')->nullable()->comment('申请码');
		$md->text('answer_code')->nullable()->comment('答应码');
		$md->string('fm_date')->nullable()->comment('开始时间');
		$md->string('to_date')->nullable()->comment('结束时间');
		$md->text('content')->nullable()->comment('申请内容');
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
