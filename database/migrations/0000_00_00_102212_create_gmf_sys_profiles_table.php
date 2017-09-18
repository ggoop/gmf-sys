<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysProfilesTable extends Migration {
	private $mdID = "c8e849a009cb11e796d547698ff71bb1";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.profile')->comment('参数')->tableName('gmf_sys_profiles');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->string('code')->comment('编码');
		$md->string('name')->nullable()->comment('名称');
		$md->text('memo')->nullable()->comment('备注');
		$md->enum('scope', 'gmf.sys.profile.scope.enum')->nullable()->comment('范围');
		$md->text('default_value')->nullable()->comment('默认值');
		$md->text('default_value_name')->nullable()->comment('默认值');

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
