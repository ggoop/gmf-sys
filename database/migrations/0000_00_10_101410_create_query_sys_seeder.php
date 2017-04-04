<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateQuerySysSeeder extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$exception = DB::transaction(function () {
			$id = "9314a2100a6211e79036095742576697";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.sys.language.list')->entity('gmf.sys.language')
					->fields(['id', 'code' => '编码', 'name' => '名称', 'memo' => '备注']);
			});
			$id = "6a1c80700a6911e7bee9dbc30ca2b914";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.sys.ent.list')->entity('gmf.sys.ent')
					->fields(['id', 'code' => '编码', 'name' => '名称', 'memo' => '备注']);
			});

			$id = "358d5410101e11e7b7a11dd8f04ef838";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.sys.profile.list')->entity('gmf.sys.profile')
					->fields(['id', 'code' => '编码', 'name' => '名称', 'memo' => '备注']);
			});

			$id = "358d5890101e11e786ecab74d6de29d1";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.sys.user.list')->entity('gmf.sys.user')
					->fields(['id', 'code' => '编码', 'name' => '名称', 'memo' => '备注']);
			});

			$id = "358d5930101e11e7a340b9b3adb85586";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.sys.role.list')->entity('gmf.sys.role')
					->fields(['id', 'code' => '编码', 'name' => '名称', 'memo' => '备注']);
			});

			$id = "358d59a0101e11e7826f7fee83931186";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.sys.permit.list')->entity('gmf.sys.permit')
					->fields(['id', 'code' => '编码', 'name' => '名称', 'memo' => '备注']);
			});

			$id = "358d5a00101e11e784537304083cfce7";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.sys.menu.list')->entity('gmf.sys.menu')
					->fields(['id', 'code' => '编码', 'name' => '名称', 'memo' => '备注']);
			});

			$id = "358d5a60101e11e78ec0db73b56a7b9b";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.sys.entity.list')->entity('gmf.sys.entity')
					->fields(['id', 'name' => '名称', 'comment' => '备注']);
			});

			$id = "358d5b00101e11e78cd947f3d0f5ddf9";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.sys.component.list')->entity('gmf.sys.component')
					->fields(['id', 'code' => '编码', 'name' => '名称']);
			});

			$id = "358d5ba0101e11e790ddd5e5a1599278";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.sys.query.list')->entity('gmf.sys.query')
					->fields(['id', 'code' => '编码', 'name' => '名称']);
			});
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Models\Query::where('code', 'like', 'gmf.sys.%')->delete();
	}
}
