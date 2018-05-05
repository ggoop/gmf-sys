<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAppsTable extends Migration {
	public $mdID = "172fd6e0c06111e796b1f515c759a8ff";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.app')->comment('应用')->tableName('gmf_sys_apps');
		$md->string('id', 100)->primary();
		$md->string('code')->nullable()->comment('编码');
		$md->string('secret')->nullable()->comment('secret');
		$md->string('name')->nullable()->comment('名称');
		$md->string('short_name')->nullable()->comment('简称');
		$md->string('group_name')->nullable()->comment('分组名称');
		$md->string('avatar', 500)->nullable();
		$md->integer('is_public')->default(0)->comment('是否公共');
		$md->string('gateway')->nullable()->comment('地址');
		$md->text('memo')->nullable()->comment('备注');
		$md->boolean('revoked')->default(0)->comment('注销');
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
