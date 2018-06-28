<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysSvPacksTable extends Migration {
	public $mdID = "01e86b83f9cb10d08dfd912f07e54797";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.sv.pack')->comment('服务包')->tableName('gmf_sys_sv_packs');
    $md->string('id', 100)->primary();
    $md->string('type',100)->nullable()->comment('类型');//公有服务：public,私有服务：private,应用服务：app
		$md->string('code')->nullable()->comment('编码');
    $md->string('name')->nullable()->comment('名称');
    $md->string('avatar')->nullable()->comment('图标');
    $md->string('discover')->nullable()->comment('发现地址');
    $md->string('gateway')->nullable()->comment('注册网关');
    $md->text('memo')->nullable()->comment('备注');
    $md->text('content')->nullable()->comment('介绍');
		$md->integer('released')->default(0)->comment('发布');		
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
