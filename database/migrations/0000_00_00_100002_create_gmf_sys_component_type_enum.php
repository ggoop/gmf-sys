<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysComponentTypeEnum extends Migration {
	public $mdID = "8dce25900f9511e8989cb9a8c2353bcb";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.component.type.enum')->comment('组件类型');
		$md->string('ui')->comment('UI')->default(0);
		$md->string('entity')->comment('实体')->default(1);
		$md->string('ref')->comment('参照')->default(2);
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
