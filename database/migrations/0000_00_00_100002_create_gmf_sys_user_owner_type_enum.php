<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysUserOwnerTypeEnum extends Migration {
	public $mdID = "7008142009cd11e78b8eb71714fb910d";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.user.owner.type.enum')->comment('用户类型');
		$md->string('sys')->comment('系统')->default(0);
		$md->string('creator')->comment('创建者')->default(0);
		$md->string('owner')->comment('拥有者')->default(1);
		$md->string('manager')->comment('管理者')->default(2);
		$md->string('member')->comment('成员')->default(3);
		$md->string('user')->comment('使用者')->default(4);
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
