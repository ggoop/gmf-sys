<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysEditorTemplatesTable extends Migration {
	public $mdID = "34b3d720eaf311e7b11b8bb29208a998";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.editor.template')->comment('编辑器模板')->tableName('gmf_sys_editor_templates');

		$md->string('id', 100)->primary();
		$md->entity('user', config('gmf.user.entity'))->nullable()->comment('用户');
		$md->string('code')->nullable()->comment('编码');
		$md->string('title')->nullable()->comment('名称');
		$md->string('memo')->nullable()->comment('备注');
		$md->longText('content')->nullable()->comment('备注');
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
