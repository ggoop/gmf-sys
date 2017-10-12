<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAuthorityRoleEntitiesTable extends Migration {
	private $mdID = "b80478e0af1c11e7a21e6b3538675156";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.authority.role.entity')->comment('角色-数据')->tableName('gmf_sys_authority_role_entities');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('role', 'gmf.sys.authority.role')->nullable()->comment('角色');
		$md->entity('entity', 'gmf.sys.entity')->nullable()->comment('实体');
		$md->string('filter')->nullable()->comment('条件');
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
