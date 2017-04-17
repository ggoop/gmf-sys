<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysJobsTable extends Migration {
	private $mdID = "ba15e2b021c311e78ba5e1af0b4f6052";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.jobs')->comment('调度')->tableName('gmf_sys_jobs');

		$md->bigIncrements('id');
		$md->string('queue');
		$md->longText('payload');
		$md->tinyInteger('attempts')->unsigned();
		$md->unsignedInteger('reserved_at')->nullable();
		$md->unsignedInteger('available_at');
		$md->unsignedInteger('created_at');

		$md->index(['queue', 'reserved_at']);

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
