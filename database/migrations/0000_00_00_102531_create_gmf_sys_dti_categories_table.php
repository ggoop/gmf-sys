<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysDtiCategoriesTable extends Migration {
	private $mdID = "777d004063ae11e78231e95dd4273a4c";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.dti.category')->comment('接口分类')->tableName('gmf_sys_dti_categories');
		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->nullable()->comment('名称');
		$md->string('host')->nullable()->comment('主机');
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
