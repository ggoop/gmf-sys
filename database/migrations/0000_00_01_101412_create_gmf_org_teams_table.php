<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfOrgTeamsTable extends Migration {
	private $mdID = "bfd5f8a00a9a11e7899dfb676b00c09c";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.org.team')->comment('班组')->tableName('gmf_org_teams');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('org', 'gmf.org.org')->nullable()->comment('组织');
		$md->entity('dept', 'gmf.org.dept')->nullable()->comment('部门');
		$md->entity('work', 'gmf.org.work')->nullable()->comment('工作中心');
		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->comment('名称');
		$md->text('memo')->nullable()->comment('备注');
		$md->integer('sequence')->default(0)->comment('顺序号');
		$md->timestamps();

		$md->foreign('org_id')->references('id')->on('gmf_org_orgs')->onDelete('cascade');
		$md->foreign('dept_id')->references('id')->on('gmf_org_depts')->onDelete('cascade');
		$md->foreign('work_id')->references('id')->on('gmf_org_works')->onDelete('cascade');
		$md->foreign('ent_id')->references('id')->on('gmf_sys_ents')->onDelete('cascade');

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
