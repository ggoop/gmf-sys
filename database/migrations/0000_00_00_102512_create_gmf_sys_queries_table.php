<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysQueriesTable extends Migration {
	private $mdID = "61cdcb5009cc11e7be6fbd179ec25c73";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.query')->comment('查询')->tableName('gmf_sys_queries');

		$md->string('id', 100)->primary();
		$md->string('code')->index()->comment('编码');
		$md->string('name')->nullable()->comment('名称');
		$md->text('memo')->nullable()->comment('备注');
		$md->entity('entity', 'gmf.sys.entity')->nullable()->comment('主实体');
		$md->string('seg_from')->nullable()->comment('from 语句');
		$md->string('seg_select')->nullable()->comment('select 语句');
		$md->string('seg_order')->nullable()->comment('order 语句');
		$md->string('seg_where')->nullable()->comment('where 语句');
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
