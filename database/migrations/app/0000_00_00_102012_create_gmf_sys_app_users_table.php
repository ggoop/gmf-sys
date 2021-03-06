<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAppUsersTable extends Migration {
	public $mdID = "7f5c5ed0524e11e8a893e33e86cf0025";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.app.user')->comment('应用用户')->tableName('gmf_sys_app_users');
		$md->bigIncrements('id');
		$md->entity('app', 'gmf.sys.app')->comment('应用');
		$md->entity('user', config('gmf.user.entity'));
		$md->integer('is_default')->nullable()->comment('是否默认');
		$md->enum('type', 'gmf.sys.user.owner.type.enum')->nullable()->comment('拥有类型');
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
