<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysSvServesTable extends Migration {
	public $mdID = "01e87a75350911c0b9eb97ff849cc147";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.sv.serve')->comment('服务')->tableName('gmf_sys_sv_serves');
    $md->string('id', 100)->primary();
    $md->entity('pack', 'gmf.sys.sv.pack')->comment('服务包');   
		$md->string('code')->nullable()->comment('编码');
    $md->string('name')->nullable()->comment('名称');
    $md->string('method',10)->nullable()->comment('请求方式');
    $md->string('path')->nullable()->comment('请求路径');
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
