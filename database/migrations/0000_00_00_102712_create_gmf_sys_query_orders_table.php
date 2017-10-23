<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysQueryOrdersTable extends Migration {
	public $mdID = "b06aaae00a6511e78303b13e3a4e9988";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.query.order')->comment('查询字段')->tableName('gmf_sys_query_orders');

		$md->bigIncrements('id');
		$md->entity('query', 'gmf.sys.query')->comment('查询');
		$md->string('name')->nullable()->comment('字段');
		$md->string('comment')->nullable()->comment('名称');
		$md->string('direction')->nullable()->comment('排序规则');
		$md->integer('sequence')->default(0)->comment('顺序');
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
