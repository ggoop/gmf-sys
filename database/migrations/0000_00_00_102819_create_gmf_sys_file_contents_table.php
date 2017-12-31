<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysFileContentsTable extends Migration {
	public $mdID = "319ddb20ede711e7b22f216a1b70a6a2";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.file.content')->comment('文件内容')->tableName('gmf_sys_file_contents');

		$md->string('id', 100)->primary();
		$md->entity('file', 'gmf.sys.file')->nullable()->comment('文件');
		$md->longText('data')->nullable()->comment('内容');

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
