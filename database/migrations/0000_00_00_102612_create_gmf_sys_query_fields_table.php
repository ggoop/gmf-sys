<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysQueryFieldsTable extends Migration {
	private $mdID = "61cdcc5009cc11e79223bbc050c53a5e";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.query.field')->comment('查询字段')->tableName('gmf_sys_query_fields');

		$md->bigIncrements('id');
		$md->entity('query', 'gmf.sys.query')->comment('查询');
		$md->string('path')->nullable()->comment('字段');
		$md->string('name')->nullable()->comment('名称');
		$md->boolean('hide')->default(0)->comment('隐藏');
		$md->integer('sequence')->default(0)->comment('顺序');
		$md->timestamps();

		$md->foreign('query_id')->references('id')->on('gmf_sys_queries')->onDelete('cascade');
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
