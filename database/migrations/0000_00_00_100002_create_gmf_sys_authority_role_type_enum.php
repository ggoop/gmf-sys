<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAuthorityRoleTypeEnum extends Migration {
	private $mdID = "3710bc60af0811e7b83ad3eae83818f1";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.authority.role.type.enum')->comment('角色类型');
		$md->string('admin')->comment('管理员角色')->default(0);
		$md->string('user')->comment('用户角色')->default(1);
		$md->string('private')->comment('私有角色')->default(2);
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
