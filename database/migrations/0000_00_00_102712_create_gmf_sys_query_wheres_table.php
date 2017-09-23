<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysQueryWheresTable extends Migration {
	private $mdID = "b6332500a03011e7822a3daa9af44af9";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.query.where')->comment('查询条件')->tableName('gmf_sys_query_wheres');

		$md->bigIncrements('id');
		$md->entity('query', 'gmf.sys.query')->comment('查询');
		$md->string('name')->nullable()->comment('字段');
		$md->string('comment')->nullable()->comment('名称');
		$md->boolean('hide')->default(0)->comment('隐藏');
		$md->enum('operator', 'gmf.sys.query.operator.enum')->nullable()->comment('操作符号');
		$md->string('value')->nullable()->comment('条件值');
		$md->enum('type', 'gmf.sys.query.where.type.enum')->nullable()->comment('类型'); //值，栏目
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
