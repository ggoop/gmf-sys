<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfSysMenuRelationsTable extends Migration {
	private $mdID = "362ac0500a9311e78c2779c560a6e4f5";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.sys.menu.relation')->comment('菜单关系')->tableName('gmf_sys_menu_relations');

		$md->bigIncrements('id');
		$md->entity('root', 'gmf.sys.menu')->comment('最上级菜单Id');
		$md->entity('parent', 'gmf.sys.menu')->comment('上级菜单');
		$md->entity('menu', 'gmf.sys.menu')->comment('下级菜单');
		$md->integer('sequence')->default('0')->comment('顺序号');
		$md->string('path')->nullable()->comment('路径');
		$md->timestamps();

		$md->foreign('root_id')->references('id')->on('gmf_sys_menus')->onDelete('cascade');
		$md->foreign('parent_id')->references('id')->on('gmf_sys_menus')->onDelete('cascade');
		$md->foreign('menu_id')->references('id')->on('gmf_sys_menus')->onDelete('cascade');

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
