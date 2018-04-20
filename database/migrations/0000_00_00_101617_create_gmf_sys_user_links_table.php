<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysUserLinksTable extends Migration {
	public $mdID = "0e67a4f0445c11e8b563f52650a76969";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.user.link')->comment('用户关联')->tableName('gmf_sys_user_links');
		$md->bigIncrements('id');
		$md->entity('fm_user', 'gmf.sys.user')->nullable()->comment('来源用户');
		$md->entity('to_user', 'gmf.sys.user')->nullable()->comment('目标用户');
		$md->boolean('is_default')->default(0)->comment('默认账号');
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
