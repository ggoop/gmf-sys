<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysQueryWhereTypeEnum extends Migration {
	private $mdID = "b6332760a03011e7910811d937ac140a";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.query.where.type.enum')->comment('查询条件类型');
		$md->string('value')->comment('值')->default(0);
		$md->string('field')->comment('字段')->default(1);
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
