<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysDbHisTable extends Migration {
	private $mdID = "c8e846e009cb11e7859585dac2f8c3e1";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.dbhis')->comment('数据备份')->tableName('gmf_sys_dbhis');

		$md->bigIncrements('id');
		$md->string('table_name')->nullable()->comment('表名称');
		$md->string('row_id', 100)->comment('原始数据Id');
		$md->string('row_type')->comment('名称');
		$md->longText('data')->comment('原始数据');

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
