<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysQueryOperatorEnum extends Migration {
	public $mdID = "36105f10712611e7a14f7d7ca6ff731b";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEnum('gmf.sys.query.operator.enum')->comment('查询操作符号');

		$md->string('equal')->comment('等于')->default(0);
		$md->string('not_equal')->comment('不等于')->default(1);
		$md->string('greater_than')->comment('大于')->default(1);
		$md->string('less_than')->comment('小于')->default(1);
		$md->string('greater_than_equal')->comment('大于等于')->default(1);
		$md->string('less_than_equal')->comment('小于等于')->default(1);
		$md->string('between')->comment('两者之间')->default(1);
		$md->string('not_between')->comment('不在两者之间')->default(1);
		$md->string('in')->comment('在之中')->default(1);
		$md->string('not_in')->comment('不在其中')->default(1);
		$md->string('null')->comment('为空')->default(1);
		$md->string('not_null')->comment('不为空')->default(1);
		$md->string('like')->comment('包含')->default(1);
		$md->string('left_like')->comment('左包含')->default(1);
		$md->string('right_like')->comment('右包含')->default(1);
		$md->string('not_like')->comment('不包含')->default(1);
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
