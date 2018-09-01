<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysEntsTable extends Migration {
	public $mdID = "c8e8490009cb11e786debfac5934e0ca";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.ent')->comment('企业')->tableName('gmf_sys_ents');
		$md->string('id', 100)->primary();
		$md->string('openid')->nullable()->comment('开放ID');
		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->nullable()->comment('名称');
		$md->string('token')->nullable()->comment('token');
		$md->integer('published')->nullable()->comment('是否发布');
		$md->integer('scope')->nullable()->comment('可见范围');
		$md->string('short_name')->nullable()->comment('简称');
		$md->string('avatar')->nullable()->comment('图标');
		$md->text('memo')->nullable()->comment('备注');

		$md->string('discover')->nullable()->comment('发现地址');
		$md->string('gateway')->nullable()->comment('注册网关');

		$md->string('industry')->nullable()->comment('行业');
		$md->string('area')->nullable()->comment('地区');
		$md->enum('status', 'gmf.sys.ent.status.enum')->nullable()->comment('状态');
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
