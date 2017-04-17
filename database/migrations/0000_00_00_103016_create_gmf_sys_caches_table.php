<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysCachesTable extends Migration {
	private $mdID = "ba15e55021c311e7885175eea90aa49b";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.cache')->comment('缓存')->tableName('gmf_sys_caches');

		$md->string('key')->unique();
		$md->text('value');
		$md->integer('expiration');
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
