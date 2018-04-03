<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysNotificationsTable extends Migration {
	public $mdID = "69d84060e52311e78485cd2663ee34f4";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.notification')->comment('消息通知')->tableName('gmf_sys_notifications');

		$md->string('id', 100)->primary();
		$md->string('type')->index()->comment('类型');
		$md->string('via')->nullable()->comment('频道'); //mail,database,sms
		$md->entity('fm_user', config('gmf.user.entity'))->nullable()->comment('来源用户');
		$md->entity('user', config('gmf.user.entity'))->nullable()->comment('用户');
		$md->string('src_id')->nullable()->comment('来源ID');
		$md->string('src_type')->nullable()->comment('来源TYPE');
		$md->text('content')->nullable()->comment('内容');
		$md->timestamp('arrived_at')->nullable();
		$md->text('error')->nullable()->comment('错误');
		$md->boolean('is_completed')->default(0)->comment('已完成');
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
