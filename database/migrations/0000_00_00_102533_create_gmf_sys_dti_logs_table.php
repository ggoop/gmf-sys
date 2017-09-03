<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysDtiLogsTable extends Migration {
	private $mdID = "abc6d52062c211e783fdf56437657a62";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.dti.log')->comment('接口执行日志')->tableName('gmf_sys_dti_logs');
		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('dti', 'gmf.sys.dti')->nullable()->comment('接口');
		$md->string('session')->nullable()->comment('回话');
		$md->timestamp('date')->nullable()->comment('日期');
		$md->timestamp('begin_date')->nullable()->comment('开始时间');
		$md->timestamp('end_date')->nullable()->comment('结果时间');
		$md->text('memo')->nullable()->comment('备注');
		$md->text('msg')->nullable()->comment('消息');
		$md->longText('content')->nullable()->comment('内容');
		$md->enum('state', 'gmf.sys.dti.state.enum')->nullable()->comment('状态');
		$md->timestamps();

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
