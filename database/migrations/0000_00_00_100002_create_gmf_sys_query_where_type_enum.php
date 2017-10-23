<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysQueryWhereTypeEnum extends Migration {
	public $mdID = "b6332760a03011e7910811d937ac140a";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.query.where.type.enum')->comment('查询条件类型');
		$md->string('value')->comment('值')->default(0);
		$md->string('ref')->comment('参照')->default(3);
		$md->string('date')->comment('日期')->default(4);
		$md->string('enum')->comment('枚举')->default(5);

		$md->string('field')->comment('字段')->default(10);
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
