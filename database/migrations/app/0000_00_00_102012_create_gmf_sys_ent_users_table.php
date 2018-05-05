<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysEntUsersTable extends Migration {
	public $mdID = "c8e8495009cb11e793acff00e76e4941";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.ent.user')->comment('企业用户')->tableName('gmf_sys_ent_users');
		$md->bigIncrements('id');
		$md->entity('ent', 'gmf.sys.ent')->comment('企业');
		$md->entity('user', config('gmf.user.entity'));
		$md->integer('is_default')->nullable()->comment('是否默认');
		$md->enum('type', 'gmf.sys.user.owner.type.enum');
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
