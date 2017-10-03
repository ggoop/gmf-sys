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
		$md->string('name')->index()->comment('编码');
		$md->string('comment')->nullable()->comment('名称');
		$md->text('memo')->nullable()->comment('备注');
		$md->enum('type', 'gmf.sys.query.type.enum')->nullable()->comment('查询类型');
		$md->entity('entity', 'gmf.sys.entity')->nullable()->comment('主实体');
		$md->string('matchs')->nullable()->comment('查询匹配字段');
		$md->string('filter')->nullable()->comment('过滤器');
		$md->integer('size')->default(0)->comment('每页显示');

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
