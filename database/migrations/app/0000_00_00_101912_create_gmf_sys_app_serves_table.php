<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAppServesTable extends Migration {
	public $mdID = "01e86b83f9cb10d08dfd912f07e54797";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.app.serve')->comment('应用服务')->tableName('gmf_sys_app_serves');
		$md->string('id', 100)->primary();
		$md->entity('app', 'gmf.sys.app')->comment('应用');
		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->nullable()->comment('名称');

		$md->string('method',10)->nullable()->comment('方法');
		$md->string('path',10)->nullable()->comment('路径');
		$md->integer('is_public')->default(0)->comment('是否公共');		
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
