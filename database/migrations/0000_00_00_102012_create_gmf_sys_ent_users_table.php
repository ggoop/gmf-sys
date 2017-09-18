<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysEntUsersTable extends Migration {
	private $mdID = "c8e8495009cb11e793acff00e76e4941";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.ent.user')->comment('企业用户')->tableName('gmf_sys_ent_users');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent');
		$md->entity('user', 'gmf.sys.user');
		$md->enum('type', 'gmf.sys.ent.user.type.enum');
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
