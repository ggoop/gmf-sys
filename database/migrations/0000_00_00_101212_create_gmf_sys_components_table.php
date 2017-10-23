<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysComponentsTable extends Migration {
	public $mdID = "c8e8476009cb11e7b1afa51fb4496b2f";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.component')->comment('组件')->tableName('gmf_sys_components');

		$md->string('id', 100)->primary();
		$md->string('code')->comment('编码');
		$md->string('name')->nullable()->comment('名称');
		$md->text('memo')->nullable()->comment('备注');
		$md->string('path')->nullable()->comment('路径');
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
