<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysLnsRequestsTable extends Migration {
	public $mdID = "033b23e0be0a11e7ad393be82211e8ea";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.lns.request')->comment('许可申请')->tableName('gmf_sys_lns_requests');

		$md->string('id')->primary();
		$md->text('serial')->nullable()->comment('序列号');
		$md->text('code')->nullable()->comment('申请码');
		$md->string('fm_date')->nullable()->comment('开始时间');
		$md->string('to_date')->nullable()->comment('结束时间');
		//code:number,
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
