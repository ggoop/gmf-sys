<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysLanguagesTable extends Migration {
	public $mdID = "c8e8481009cb11e7aca01bea5e7f1af7";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.language')->comment('语言')->tableName('gmf_sys_languages');

		$md->string('id', 100)->primary();
		$md->string('code')->unique()->comment('编码');
		$md->string('name')->comment('名称');
		$md->string('memo')->nullable()->comment('备注');
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
