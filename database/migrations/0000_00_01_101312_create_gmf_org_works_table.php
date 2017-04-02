<?php

use Gmf\Sys\Database\Metadata;
use Illuminate\Database\Migrations\Migration;

class CreateGmfOrgWorksTable extends Migration {
	private $mdID = "b3894cd00a9a11e7a21613e4b2686aa1";
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$md = Metadata::create($this->mdID);
		$md->mdEntity('gmf.org.work')->comment('工作中心')->tableName('gmf_org_works');

		$md->string('id', 100)->primary();
		$md->entity('ent', 'gmf.sys.ent')->nullable()->comment('企业');
		$md->entity('org', 'gmf.org.org')->nullable()->comment('组织');
		$md->entity('dept', 'gmf.org.dept')->nullable()->comment('部门');

		$md->string('code')->nullable()->comment('编码');
		$md->string('name')->comment('名称');
		$md->text('memo')->nullable()->comment('备注');

		$md->integer('sequence')->default(0)->comment('顺序号');
		$md->timestamps();

		$md->foreign('org_id')->references('id')->on('gmf_org_orgs')->onDelete('cascade');
		$md->foreign('dept_id')->references('id')->on('gmf_org_depts')->onDelete('cascade');
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
