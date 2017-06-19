<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysVisitorsTable extends Migration {
	private $mdID = "61cdcca009cc11e7a5a4edfb155220c6";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.visitor')->comment('访问记录')->tableName('gmf_sys_visitors');

		$md->bigIncrements('id');
		$md->entity('user', 'gmf.sys.user')->nullable();
		$md->string('path')->nullable();
		$md->string('url', 500)->nullable();
		$md->string('ip')->nullable();
		$md->string('method')->nullable();
		$md->longText('params')->nullable();
		$md->string('agent', 500)->nullable();
		$md->string('referer', 500)->nullable();
		//请求总时间.秒
		$md->float('times')->default(0);
		//业务执行时间.秒
		$md->float('actimes')->default(0);
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
