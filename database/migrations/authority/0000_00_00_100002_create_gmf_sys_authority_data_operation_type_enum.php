<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAuthorityDataOperationTypeEnum extends Migration {
	public $mdID = "cc953850af1711e7a0956954a0bb9d26";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.authority.data.operation.type.enum')->comment('数据操作类型');
		$md->string('all')->comment('全部')->default(0);
		$md->string('r')->comment('读取')->default(1);
		$md->string('cud')->comment('增、删、改')->default(2);
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
