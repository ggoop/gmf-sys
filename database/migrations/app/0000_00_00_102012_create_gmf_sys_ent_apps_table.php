<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysEntAppsTable extends Migration {
	public $mdID = "ab3063c04f6f11e8bfb0d9000adcab84";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.ent.app')->comment('企业应用')->tableName('gmf_sys_ent_apps');

		$md->bigIncrements('id');
		$md->entity('ent', 'gmf.sys.ent')->comment('企业');
		$md->entity('app', 'gmf.sys.app')->comment('应用');
		$md->string('secret')->nullable()->comment('secret');
		$md->string('gateway')->nullable()->comment('地址');
		$md->integer('is_default')->nullable()->comment('是否默认');
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
