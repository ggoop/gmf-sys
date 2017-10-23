<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysDtiParamsTable extends Migration {
	public $mdID = "f982bf007e3911e78fd26188c662f5c1";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.dti.param')->comment('接口上下文')->tableName('gmf_sys_dti_params');
		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('category', 'gmf.sys.dti.category')->nullable()->comment('分类');
		$md->entity('dti', 'gmf.sys.dti')->nullable()->comment('接口');

		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->nullable()->comment('名称');

		$md->enum('type', 'gmf.sys.dti.param.type.enum')->nullable()->comment('参数类型');
		$md->string('value', 500)->nullable()->comment('参数值');
		$md->boolean('is_revoked')->default(0)->comment('注销');

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
