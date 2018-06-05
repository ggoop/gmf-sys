<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysVisitorsTable extends Migration {
	public $mdID = "61cdcca009cc11e7a5a4edfb155220c6";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID, [], 'log');
		$md->mdEntity('gmf.sys.visitor')->comment('访问记录')->tableName('gmf_sys_visitors');

		$md->bigIncrements('id');
		$md->entity('user', config('gmf.user.entity'))->nullable();
		$md->string('user_name')->nullable();

		$md->entity('ent', 'gmf.sys.ent')->nullable();
		$md->text('path')->nullable();
		$md->text('url')->nullable();
		$md->string('ip')->nullable();
		$md->string('method')->nullable();
		$md->longText('input')->nullable();
		$md->longText('query')->nullable();
		$md->longText('body')->nullable();
		$md->longText('header')->nullable();
		$md->text('agent')->nullable();
		$md->text('referer')->nullable();
		$md->text('content_type')->nullable();
		$md->longText('trace')->nullable();

		//应用名称
		$md->string('app_name')->nullable();
		$md->string('app_id')->nullable();

		//客户名称
		$md->text('client_name')->nullable();
		//产品序列号
		$md->text('client_sn')->nullable();
		$md->text('client_id')->nullable();
		//登录帐号
		$md->text('client_account')->nullable();

		//请求总时间.秒
		$md->float('times')->default(0);
		//业务执行时间.秒
		$md->float('actimes')->default(0);
		$md->timestamp('created_at');

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
