<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysSessionsTable extends Migration {
	private $mdID = "ba15e5c021c311e7b080b30c8c73e10e";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.session')->comment('会话')->tableName('gmf_sys_sessions');

		$md->string('id')->unique();
		$md->integer('user_id')->nullable();
		$md->string('ip_address', 45)->nullable();
		$md->text('user_agent')->nullable();
		$md->text('payload');
		$md->integer('last_activity');
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
