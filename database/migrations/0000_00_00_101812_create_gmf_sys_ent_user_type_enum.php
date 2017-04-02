<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysEntUserTypeEnum extends Migration {
	private $mdID = "7008142009cd11e78b8eb71714fb910d";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.ent.user.typeEnum')->comment('用户类型');
		$md->string('create')->comment('创建者')->default(0);
		$md->string('owner')->comment('拥有者')->default(1);
		$md->string('member')->comment('成员')->default(1);
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
