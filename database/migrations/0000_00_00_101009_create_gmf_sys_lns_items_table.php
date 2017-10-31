<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysLnsItemsTable extends Migration {
	public $mdID = "8118fe300a9d11e78823076f4d2525cf";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.lns.item')->comment('许可号')->tableName('gmf_sys_lns_items');

		$md->string('id')->primary();
		$md->string('type')->nullable()->comment('拥有者类型');
		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->nullable()->comment('名称');
		$md->string('field')->nullable()->comment('字段');
		$md->string('filter')->nullable()->comment('条件');

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
