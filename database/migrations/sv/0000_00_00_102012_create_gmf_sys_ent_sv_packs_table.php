<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysEntSvPacksTable extends Migration {
	public $mdID = "01e87a76b35e19c0b2c0c1027e5e7655";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.ent.sv.pack')->comment('企业服务包')->tableName('gmf_sys_ent_sv_packs');

		$md->bigIncrements('id');
		$md->entity('ent', 'gmf.sys.ent')->comment('企业');
    $md->entity('pack', 'gmf.sys.sv.pack')->comment('服务包');
    $md->enum('type', 'gmf.sys.user.owner.type.enum')->nullable()->comment('拥有类型');
		$md->string('token')->nullable()->comment('token');
		$md->string('discover')->nullable()->comment('发现地址');
		$md->string('gateway')->nullable()->comment('注册网关');
		$md->integer('is_default')->nullable()->comment('是否默认');
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
