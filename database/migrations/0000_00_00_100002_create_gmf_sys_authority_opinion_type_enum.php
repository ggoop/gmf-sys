<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAuthorityOpinionTypeEnum extends Migration {
	private $mdID = "3710cfe0af0811e7a7315dc7d2671892";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.authority.opinion.type.enum')->comment('建议');
		$md->string('permit')->comment('允许')->default(0);
		$md->string('deny')->comment('拒绝')->default(1);
		$md->string('password')->comment('密码')->default(2);
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
