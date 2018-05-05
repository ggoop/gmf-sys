<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysDtiMethodEnum extends Migration {
	public $mdID = "0e3ad6109b9211e79822cdba17fcc670";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.dti.method.enum')->comment('请求类型');
		$md->string('post')->comment('post')->default(0);
		$md->string('get')->comment('get')->default(1);
		$md->string('put')->comment('put')->default(2);
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
