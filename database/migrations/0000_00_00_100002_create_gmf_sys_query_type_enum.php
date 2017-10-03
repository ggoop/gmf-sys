<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysQueryTypeEnum extends Migration {
	private $mdID = "93ab8970a80711e7abfe8fef87a63a92";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.query.type.enum')->comment('查询类型');
		$md->string('entity')->comment('实体')->default(0);
		$md->string('case')->comment('方案')->default(1);
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
