<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysDtiParamTypeEnum extends Migration {
	private $mdID = "16092a207e3b11e7af0ed1225600ff9b";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.dti.param.type.enum')->comment('参数值类型');
		$md->string('fixed')->comment('固定值')->default(0);
		$md->string('input')->comment('输入值')->default(1);
		$md->string('expression')->comment('表达式')->default(2);
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
