<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models\Menu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMenuOrgSeeder extends Migration {
	private $sequence = '15';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$exception = DB::transaction(function () {
			$id = "d8540a600a0311e785189b0fc663edd9";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->root_id($id)->code('org')->name('组织架构')
					->uri('org')->sequence($this->sequence . '0000');
			});
			$id = "d8540c400a0311e78a2b93658d06a5a4";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('org.org')->name('组织机构')->parent('org')
					->uri('org.org.list')->sequence($this->sequence . '0100');
			});
			$id = "d8540ce00a0311e782168b802ae641fe";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('org.dept')->name('部门')->parent('org')
					->uri('org.dept.list')->sequence($this->sequence . '0200');
			});
			$id = "d8540d400a0311e7a09d111e03f0b260";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('org.work')->name('工作中心')->parent('org')
					->uri('org.work.list')->sequence($this->sequence . '0300');
			});
			$id = "d8540da00a0311e7b66a035a9e6c2a3c";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('org.team')->name('班组')->parent('org')
					->uri('org.team.list')->sequence($this->sequence . '0400');
			});
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Menu::where('code', 'like', 'org%')->delete();
	}
}
