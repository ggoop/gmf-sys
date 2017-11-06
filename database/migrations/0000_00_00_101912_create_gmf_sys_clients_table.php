<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysClientsTable extends Migration {
	public $mdID = "172fd6e0c06111e796b1f515c759a8ff";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.client')->comment('客户端')->tableName('gmf_sys_clients');
		$md->string('id', 100)->primary();
		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->nullable()->comment('名称');
		$md->text('memo')->nullable()->comment('备注');
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
