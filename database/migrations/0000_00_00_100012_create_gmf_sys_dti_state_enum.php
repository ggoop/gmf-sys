<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysDtiStateEnum extends Migration {
	public $mdID = "f06d122062c311e783db8bd410b6ee0f";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.dti.state.enum')->comment('接口状态');
		$md->string('waiting')->comment('等待执行')->default(0);
		$md->string('runing')->comment('正在运行')->default(1);
		$md->string('succeed')->comment('成功的')->default(2);
		$md->string('failed')->comment('失败的')->default(3);

		$md->string('disabled')->comment('停用的')->default(10);
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
