<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysFilesTable extends Migration {
	public $mdID = "6470daa0c6eb11e7993795dfb14b2266";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.file')->comment('文件')->tableName('gmf_sys_files');

		$md->string('id', 100)->primary();

		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('user', config('gmf.user.entity'))->nullable()->comment('用户');

		$md->string('disk')->default('public')->nullable()->comment('磁盘');
		$md->string('code')->comment('编码');

		$md->string('type')->nullable()->comment('类型');
		$md->string('title')->nullable()->comment('标题');
		$md->longText('data')->nullable()->comment('内容');

		$md->string('ext')->nullable()->comment('扩展名');
		$md->string('path')->nullable()->comment('名称');
		$md->string('url')->nullable()->comment('地址');
		$md->integer('size')->nullable()->comment('大小');
		$md->text('props')->nullable()->comment('属性');

		$md->boolean('is_revoked')->default(0)->comment('撤销');

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
