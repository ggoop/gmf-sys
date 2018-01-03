<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysJobsTable extends Migration {
	public $mdID = "f994c190f02111e7ba1e23cdceaff63e";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.job')->comment('任务')->tableName('gmf_sys_jobs');

		$md->bigIncrements('id', 100)->primary();
		$md->string('queue')->index()->comment('类型');
		$md->longText('payload')->nullable()->comment('来源ID');
		$md->integer('attempts')->nullable()->comment('附加次数');
		$md->integer('reserved_at')->nullable()->comment('等待时间');
		$md->integer('available_at')->nullable()->comment('身份标识');
		$md->integer('created_at')->nullable()->comment('创建时间');

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
