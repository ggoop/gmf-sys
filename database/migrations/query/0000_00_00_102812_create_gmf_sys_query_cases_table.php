<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysQueryCasesTable extends Migration {
	public $mdID = "61cdcbe009cc11e79ad38540a1ea5455";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.query.case')->comment('查询方案')->tableName('gmf_sys_query_cases');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('query', 'gmf.sys.query')->comment('查询');
		$md->string('name')->nullable()->comment('名称');
		$md->string('comment')->nullable()->comment('描述');
		$md->entity('user', config('gmf.user.entity'))->nullable();
		$md->longText('data')->nullable()->comment('数据');
		$md->integer('size')->nullable()->comment('名称');
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
