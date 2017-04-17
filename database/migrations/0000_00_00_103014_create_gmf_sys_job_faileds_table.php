<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysJobFailedsTable extends Migration {
	private $mdID = "ba15e4e021c311e78aa3890368d9572f";

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.job.faileds')->comment('调度失败')->tableName('gmf_sys_job_faileds');

		$md->bigIncrements('id');
		$md->text('connection');
		$md->text('queue');
		$md->longText('payload');
		$md->longText('exception');
		$md->timestamp('failed_at')->useCurrent();

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
