<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysTransfersTable extends Migration {
	public $mdID = "fa188d20f41011e7a56badc6967ef5dc";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.transfers')->comment('数据迁移')->tableName('gmf_sys_transfers');

		$md->bigIncrements('id');
		$md->string('client_id')->nullable()->comment('应用ID');
		$md->string('client_key')->nullable()->comment('应用秘钥');
		$md->string('indentifier')->nullable()->comment('身份标识');
		$md->string('src_id')->nullable()->comment('来源ID');
		$md->string('src_type')->nullable()->comment('来源类型');
		$md->string('target_id')->nullable()->comment('来源ID');
		$md->string('target_type')->nullable()->comment('来源类型');
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
