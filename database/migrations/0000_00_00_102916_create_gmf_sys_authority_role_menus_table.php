<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAuthorityRoleMenusTable extends Migration {
	private $mdID = "c87b2350af1811e78c78efbf9520c81a";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.authority.role.menu')->comment('角色-菜单')->tableName('gmf_sys_authority_role_menus');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('role', 'gmf.sys.authority.role')->nullable()->comment('角色');
		$md->entity('menu', 'gmf.sys.menu')->nullable()->comment('菜单');
		$md->enum('opinion', 'gmf.sys.authority.opinion.type.enum')->nullable()->comment('建议');
		$md->boolean('is_revoked')->default(0)->comment('注销');
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
