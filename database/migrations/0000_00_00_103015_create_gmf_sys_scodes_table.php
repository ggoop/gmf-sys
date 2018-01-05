<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysScodesTable extends Migration {
	public $mdID = "56ccd210f21e11e798d45d0d9c695898";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.scode')->comment('短码')->tableName('gmf_sys_scodes');

		$md->string('id', 100)->primary();
		$md->string('client_id')->nullable()->comment('应用ID');
		$md->string('client_key')->nullable()->comment('应用秘钥');
		$md->entity('user', config('gmf.user.entity'))->nullable()->comment('用户');
		$md->string('type')->nullable()->comment('类型');
		$md->string('code')->nullable()->comment('短码');
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
