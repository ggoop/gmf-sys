<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysEntLnsTable extends Migration {
	public $mdID = "84c5a7c0b7d711e79434a7e4ea4de71b";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.ent.lns')->comment('企业许可')->tableName('gmf_sys_ent_lns');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('lns', 'gmf.sys.lns')->nullable()->comment('许可');
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
