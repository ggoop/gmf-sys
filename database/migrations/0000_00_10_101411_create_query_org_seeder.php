<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateQueryOrgSeeder extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$exception = DB::transaction(function () {
			$id = "a84fbb20107f11e7941defd9424c1f54";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('gmf.org.org.list')->entity('gmf.org.org')
					->fields(['id', 'code' => '编码', 'name' => '名称', 'memo' => '备注']);
			});
			$id = "a84fbd10107f11e794d0f977720ae90b";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('gmf.org.dept.list')->entity('gmf.org.dept')
					->fields(['id', 'code' => '编码', 'name' => '名称', 'memo' => '备注']);
			});

			$id = "a84fbda0107f11e7b5c163c1cce808d9";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('gmf.org.work.list')->entity('gmf.org.work')
					->fields(['id', 'code' => '编码', 'name' => '名称', 'memo' => '备注']);
			});

			$id = "a84fbdf0107f11e7a26421a08bab0d27";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('gmf.org.team.list')->entity('gmf.org.team')
					->fields(['id', 'code' => '编码', 'name' => '名称', 'memo' => '备注']);
			});
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Models\Query::where('code', 'like', 'gmf.org.%.list')->delete();
	}
}
