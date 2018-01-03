<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysJobFailedsTable extends Migration {
	public $mdID = "da45b090f02311e7a61d1bb828623084";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.job.failed')->comment('失败的任务')->tableName('gmf_sys_job_faileds');

		$md->bigIncrements('id');
		$md->text('connection');
		$md->text('queue');
		$md->longText('payload');
		$md->longText('exception');
		$md->timestamp('failed_at')->nullable()->comment('创建时间')->useCurrent();

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
