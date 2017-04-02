<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysProfileScopeEnum extends Migration {
	private $mdID = "7008152009cd11e79c4af9baacb8f332";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.profile.scopeEnum')->comment('参数范围');
		$md->string('sys')->comment('系统')->default(0);
		$md->string('app')->comment('应用')->default(1);
		$md->string('ent')->comment('企业')->default(1);
		$md->string('user')->comment('用户')->default(1);
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
