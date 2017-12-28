<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAuthVcodesTable extends Migration {
	public $mdID = "40a12970eba511e7abd4a38971be6c0f";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.auth.vcode')->comment('验证码')->tableName('gmf_sys_auth_vcodes');
		$md->string('id')->primary();
		$md->entity('user', 'gmf.sys.user')->nullable()->comment('用户');
		$md->string('type')->nullable()->index();
		$md->string('token')->index();
		$md->timestamp('expire_time')->nullable();
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
