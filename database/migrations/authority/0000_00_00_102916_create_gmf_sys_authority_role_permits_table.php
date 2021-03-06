<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAuthorityRolePermitsTable extends Migration {
	public $mdID = "c87b2200af1811e7baa3eb1e54d7d0d4";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.authority.role.permit')->comment('角色-权限')->tableName('gmf_sys_authority_role_permits');

		$md->bigIncrements('id');
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('role', 'gmf.sys.authority.role')->nullable()->comment('角色');
		$md->entity('permit', 'gmf.sys.authority.permit')->nullable()->comment('权限');
		$md->enum('opinion', 'gmf.sys.authority.opinion.type.enum')->default('permit')->comment('建议');
		$md->boolean('revoked')->default(0)->comment('注销');
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
