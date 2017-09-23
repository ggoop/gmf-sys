<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysDtiLocalsTable extends Migration {
	private $mdID = "0599a7f09ac211e7a8046d95888581cc";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.dti.local')->comment('接口')->tableName('gmf_sys_dti_locals');

		$md->string('id', 100)->primary();
		$md->string('code')->comment('编码');
		$md->string('name')->nullable()->comment('名称');

		$md->text('memo')->nullable()->comment('备注');
		$md->boolean('is_revoked')->default(0)->comment('注销');

		$md->string('host')->nullable()->comment('接口主机');
		$md->enum('method', 'gmf.sys.dti.method.enum')->default('post')->nullable()->comment('接口方法');
		$md->string('path')->nullable()->comment('接口路径');
		$md->text('header')->nullable()->comment('请求头');
		$md->text('body')->nullable()->comment('请求体');
		$md->text('query')->nullable()->comment('请求参数');
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
