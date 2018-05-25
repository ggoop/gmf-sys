<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysLogsTable extends Migration {
	public $mdID = "01e8595ec84a1a009f0ca7676ff266f6";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID, [], 'log');
		$md->mdEntity('gmf.sys.log')->comment('日志')->tableName('gmf_sys_logs');

		$md->bigIncrements('id');
		$md->entity('user', config('gmf.user.entity'))->nullable();
		$md->entity('ent', 'gmf.sys.ent')->nullable();
		$md->entity('app', 'gmf.sys.app')->nullable()->comment('应用');
		$md->enum('level', 'gmf.sys.log.level.enum')->nullable();
		$md->string('object')->nullable();
		$md->string('server')->nullable();
		$md->string('client')->nullable();
		$md->longText('input')->nullable();
		$md->longText('content')->nullable();
		$md->timestamp('created_at');

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
