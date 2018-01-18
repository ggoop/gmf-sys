<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysActivitiesTable extends Migration {
	public $mdID = "7c374880e52511e78624e129444b1b68";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		//谁在这里做了什么
		$md->mdEntity('gmf.sys.activity')->comment('行为活动')->tableName('gmf_sys_activities');

		$md->string('id', 100)->primary();
		$md->string('type')->index()->comment('类型');
		$md->entity('user', config('gmf.user.entity'))->nullable()->comment('用户');

		$md->string('what_id')->nullable()->comment('事情ID');
		$md->string('what_type')->nullable()->comment('事情TYPE');

		$md->string('causer_id')->nullable()->comment('引起者ID');
		$md->string('causer_type')->nullable()->comment('引起者TYPE');

		$md->string('indentifier')->nullable()->comment('身份标识');

		$md->text('content')->nullable()->comment('内容');
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
