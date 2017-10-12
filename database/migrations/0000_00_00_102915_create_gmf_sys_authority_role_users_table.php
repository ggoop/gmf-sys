<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAuthorityRoleUsersTable extends Migration {
	private $mdID = "c87b19b0af1811e788c2f581eb451196";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.authority.role.user')->comment('角色用户')->tableName('gmf_sys_authority_role_users');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('role', 'gmf.sys.authority.role')->nullable()->comment('角色');
		$md->entity('user', config('gmf.user.entity'))->nullable()->comment('用户');
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
