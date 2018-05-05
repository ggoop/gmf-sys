<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysEntAppUsersTable extends Migration {
	public $mdID = "4737b5404f7011e885bf951ff1497a46";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.ent.app.user')->comment('企业应用用户')->tableName('gmf_sys_ent_app_users');

		$md->bigIncrements('id');
		$md->entity('ent', 'gmf.sys.ent')->comment('企业');
		$md->entity('app', 'gmf.sys.app')->comment('应用');
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
