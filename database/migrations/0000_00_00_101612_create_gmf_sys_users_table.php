<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysUsersTable extends Migration {
	public $mdID = "c8e8486009cb11e7b0899dc169137f43";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.user')->comment('用户')->tableName('gmf_sys_users');
		$md->string('id', 100)->primary();

		$md->string('mobile', 20)->nullable();
		$md->string('email')->nullable();
		$md->string('account')->nullable();
		$md->string('password')->nullable();
		$md->string('secret')->nullable();

		$md->string('name')->nullable();
		$md->string('gender')->nullable();
		$md->string('nick_name')->nullable();

		$md->string('type')->nullable()->comment('类型');
		$md->string('avatar', 500)->nullable();
		$md->string('cover', 500)->nullable();
		$md->string('memo', 500)->nullable()->comment('备注');

		$md->boolean('email_verified')->default(false)->comment('邮件认证');
		$md->boolean('mobile_verified')->default(false)->comment('手机认证');
		$md->enum('status', 'gmf.sys.user.status.enum')->nullable();

		$md->string('client_id')->nullable()->comment('应用ID');
		$md->string('client_type')->nullable()->comment('应用类型');
		$md->string('client_name')->nullable()->comment('应用名称');

		$md->string('src_id', 200)->nullable()->comment('第三方用户id');
		$md->text('src_url')->nullable()->comment('账号来源地址');

		$md->text('info')->nullable();

		$md->rememberToken();
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
