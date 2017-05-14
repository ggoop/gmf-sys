<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysEntsTable extends Migration {
	private $mdID = "c8e8490009cb11e786debfac5934e0ca";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.ent')->comment('企业')->tableName('gmf_sys_ents');
		$md->string('id', 100)->primary();
		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->nullable()->comment('名称');
		$md->text('memo')->nullable()->comment('备注');
		$md->string('shortName')->nullable()->comment('简称');
		$md->string('avatar')->nullable()->comment('图标');
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
