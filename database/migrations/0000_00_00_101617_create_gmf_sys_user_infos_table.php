<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysUserInfosTable extends Migration {
	public $mdID = "ce11b060edf111e796a8e7474d41c783";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.user.info')->comment('账号')->tableName('gmf_sys_user_infos');
		$md->bigIncrements('id');
		$md->entity('user', 'gmf.sys.user')->nullable()->comment('用户');
		$md->string('type')->nullable()->comment('类型');
		$md->text('content')->nullable()->comment('内容');

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
