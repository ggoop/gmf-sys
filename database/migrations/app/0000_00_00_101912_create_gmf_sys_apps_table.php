<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysAppsTable extends Migration {
	public $mdID = "172fd6e0c06111e796b1f515c759a8ff";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.app')->comment('应用')->tableName('gmf_sys_apps');
		$md->string('id', 100)->primary();
    $md->string('openid')->nullable()->comment('开放ID');
    $md->string('secret')->nullable()->comment('应用秘钥');
    $md->string('type',100)->nullable()->comment('类型');//ios,android,h5,e
		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->nullable()->comment('名称');
    $md->string('token')->nullable()->comment('令牌');
    $md->string('aeskey')->nullable()->comment('数据加密秘钥');
    $md->string('callback_url')->nullable()->comment('回调地址');

		$md->string('short_name')->nullable()->comment('简称');
		$md->string('group_name')->nullable()->comment('分组名称');
		$md->string('avatar', 500)->nullable();
		
		$md->integer('is_public')->default(0)->comment('是否公共');

		$md->string('discover')->nullable()->comment('发现地址');
		$md->string('gateway')->nullable()->comment('注册网关');
		$md->text('memo')->nullable()->comment('备注');
		$md->enum('status', 'gmf.sys.ent.app.enum')->nullable()->comment('状态');
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
