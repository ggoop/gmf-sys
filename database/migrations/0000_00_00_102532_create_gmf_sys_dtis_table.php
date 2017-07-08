<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysDtisTable extends Migration {
	private $mdID = "abc6d13062c211e78b08d95d1602c897";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.dti')->comment('接口')->tableName('gmf_sys_dtis');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->string('code')->comment('编码');
		$md->string('name')->nullable()->comment('名称');
		$md->entity('category', 'gmf.sys.dti.category')->nullable()->comment('分类');
		$md->string('host')->nullable()->comment('主机');
		$md->string('path')->nullable()->comment('路径');
		$md->string('method')->default('post')->comment('方法');
		$md->integer('sequence')->default(0)->comment('顺序');
		$md->text('params')->nullable()->comment('参数');

		$md->timestamp('begin_date')->nullable()->comment('开始时间');
		$md->timestamp('end_date')->nullable()->comment('结果时间');
		$md->boolean('is_running')->default(0)->comment('正在执行');
		$md->boolean('is_revoked')->default(0)->comment('注销');
		$md->string('memo')->nullable()->comment('备注');
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
