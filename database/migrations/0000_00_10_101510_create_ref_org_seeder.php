<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRefOrgSeeder extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$exception = DB::transaction(function () {
			$id = "220f0760108a11e7afcfcf76a541820f";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.org.org.ref')->entity('gmf.org.org')
					->fields(['id', 'code' => '编码', 'name' => '名称']);
			});
			$id = "220f09c0108a11e7a26d4f2eaa45bc8e";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.org.dept.ref')->entity('gmf.org.dept')
					->fields(['id', 'code' => '编码', 'name' => '名称']);
			});

			$id = "220f0a80108a11e7a7d77ddc128cc28e";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.org.work.ref')->entity('gmf.org.work')
					->fields(['id', 'code' => '编码', 'name' => '名称']);
			});

			$id = "220f0b10108a11e79af6875051229d93";
			Models\Query::build(function (Builder $builder) use ($id) {
				$builder->id($id)->name('gmf.org.team.ref')->entity('gmf.org.team')
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
		Models\Query::where('code', 'like', 'gmf.org.%.ref')->delete();
	}
}
