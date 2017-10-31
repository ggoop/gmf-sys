<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysLnsAnswersTable extends Migration {
	public $mdID = "03707690be1011e7936da5575e6a36a0";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.lns.answer')->comment('许可')->tableName('gmf_sys_lns_answers');

		$md->string('id')->primary();
		$md->text('code')->nullable()->comment('答应码');

		$md->text('request_serial')->nullable()->comment('序列号');
		$md->text('request_code')->nullable()->comment('申请码');
		$md->dateTime('fm_date')->nullable()->comment('开始时间');
		$md->dateTime('to_date')->nullable()->comment('结束时间');

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
