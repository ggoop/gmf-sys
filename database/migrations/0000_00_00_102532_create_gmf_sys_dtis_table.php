<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysDtisTable extends Migration {
	public $mdID = "abc6d13062c211e78b08d95d1602c897";
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
		$md->entity('local', 'gmf.sys.dti.local')->nullable()->comment('本地接口');

		$md->text('memo')->nullable()->comment('备注');
		$md->boolean('is_revoked')->default(0)->comment('注销');

		$md->string('host')->nullable()->comment('接口主机');
		$md->enum('method', 'gmf.sys.dti.method.enum')->default('post')->nullable()->comment('接口方法');
		$md->string('path')->nullable()->comment('接口路径');

		$md->text('header')->nullable()->comment('请求头');
		$md->text('body')->nullable()->comment('请求体');
		$md->text('query')->nullable()->comment('请求参数');

		$md->integer('sequence')->default(0)->comment('顺序');

		$md->timestamp('begin_date')->nullable()->comment('开始时间');
		$md->timestamp('end_date')->nullable()->comment('结果时间');
		$md->boolean('is_running')->default(0)->comment('正在执行');

		$md->text('msg')->nullable()->comment('消息');

		$md->integer('total_runs')->default(0)->comment('执行次数');

		$md->string('data_src_identity')->nullable()->comment('数据来源身份');

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
