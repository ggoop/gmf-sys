<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models\Menu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMenuCboSeeder extends Migration {
	private $sequence = '12';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		$exception = DB::transaction(function () {
			$id = "843d0f800a0b11e79282fdcab5b97bfe";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->root_id($id)->code('cbo')->name('基础数据')
					->uri('cbo')->sequence($this->sequence . '0000');
			});
			//公共设置
			$id = "74cca1c00a0b11e790c17ff00f6c46c7";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.setting')->name('公共设置')->parent('cbo')
					->uri('cbo.setting')->sequence($this->sequence . '0100');
			});
			$id = "74cca4600a0b11e7b353d96022d2d0df";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.setting.unit')->name('计量单位')->parent('cbo.setting')
					->uri('cbo.unit.list')->sequence($this->sequence . '0101');
			});
			$id = "74cca5400a0b11e7bbe39513fefc51be";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.setting.currency')->name('币种')->parent('cbo.setting')
					->uri('cbo.currency.list')->sequence($this->sequence . '0102');
			});
			//地址
			$id = "a28f6f000a0a11e79c751d22c652d45f";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.address')->name('地址')->parent('cbo')
					->uri('cbo.address')->sequence($this->sequence . '0200');
			});
			$id = "a28f6f700a0a11e7a493ef336e695fd8";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.address.country')->name('国家')->parent('cbo.address')
					->uri('cbo.country.list')->sequence($this->sequence . '0201');
			});
			$id = "a28f6fe00a0a11e78a24a3c3944e1248";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.address.area')->name('区域')->parent('cbo.address')
					->uri('cbo.area.list')->sequence($this->sequence . '0202');
			});
			$id = "a28f70600a0a11e78f079f9e5ad5dcaa";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.address.division')->name('区划')->parent('cbo.address')
					->uri('cbo.division.list')->sequence($this->sequence . '0203');
			});
			//物料
			$id = "c6cf2e600a0a11e7b9899d7ec892f5f4";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.item')->name('物料')->parent('cbo')
					->uri('cbo.item')->sequence($this->sequence . '0300');
			});
			$id = "c6cf30500a0a11e78f81c72d4d010df0";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.item.category')->name('物料分类')->parent('cbo.item')
					->uri('cbo.item.category.list')->sequence($this->sequence . '0301');
			});
			$id = "c6cf30e00a0a11e7911007a1550c6a1d";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.item.item')->name('料品')->parent('cbo.item')
					->uri('cbo.item.list')->sequence($this->sequence . '0302');
			});
			//客商
			$id = "614ba7100a0b11e7a1f12d9698f3715b";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.trader')->name('客商')->parent('cbo')
					->uri('cbo.trader')->sequence($this->sequence . '0400');
			});
			$id = "614ba6700a0b11e7b4b36f2b36cb3070";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.trader.category')->name('客商分类')->parent('cbo.trader')
					->uri('cbo.trader.category.list')->sequence($this->sequence . '0401');
			});
			$id = "614ba2f00a0b11e792800fcda0b10aa9";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.trader.trader')->name('客商')->parent('cbo.trader')
					->uri('cbo.trader.list')->sequence($this->sequence . '0402');
			});
			//厂牌
			$id = "50e65e100a0b11e7bcba2749b385b10e";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.mfc')->name('厂牌')->parent('cbo')
					->uri('cbo.mfc')->sequence($this->sequence . '0500');
			});
			$id = "50e65b200a0b11e7adbc87796f3f83a8";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.mfc.category')->name('厂牌分类')->parent('cbo.mfc')
					->uri('cbo.mfc.category.list')->sequence($this->sequence . '0501');
			});
			$id = "50e658d00a0b11e7b60b89424846759d";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.mfc.mfc')->name('厂牌')->parent('cbo.mfc')
					->uri('cbo.mfc.list')->sequence($this->sequence . '0501');
			});
			//期间
			$id = "36b856200a0b11e79ef44f04c45fb8ea";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.period')->name('期间')->parent('cbo')
					->uri('cbo.period')->sequence($this->sequence . '0600');
			});
			$id = "36b85a700a0b11e7b29d19a90ef057eb";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.period.account')->name('会计日历')->parent('cbo.period')
					->uri('cbo.period.account.list')->sequence($this->sequence . '0601');
			});
			$id = "36b85bb00a0b11e789e147c45460f723";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.period.static')->name('统计期间')->parent('cbo.period')
					->uri('cbo.period.static.list')->sequence($this->sequence . '0602');
			});
			//人员
			$id = "2e6777300a0b11e7b78c832e35f3ca52";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.person')->name('人员')->parent('cbo')
					->uri('cbo.person')->sequence($this->sequence . '0700');
			});
			$id = "2e6779600a0b11e7a7b559c7d96adb02";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.person.person')->name('人员')->parent('cbo.person')
					->uri('cbo.person.list')->sequence($this->sequence . '0701');
			});
			//仓储
			$id = "225de7a00a0b11e7be8e110eaf903461";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.wh')->name('仓储')->parent('cbo')
					->uri('cbo.wh')->sequence($this->sequence . '0800');
			});
			$id = "225de9800a0b11e7945fa35c2a54b6aa";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.wh.wh')->name('存储地点')->parent('cbo.wh')
					->uri('cbo.wh.list')->sequence($this->sequence . '0801');
			});
			//项目
			$id = "156650900a0b11e7afd2370b8b78bf66";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.project')->name('项目')->parent('cbo')
					->uri('cbo.project')->sequence($this->sequence . '0900');
			});
			$id = "156653000a0b11e78859253b0b343559";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.project.category')->name('项目分类')->parent('cbo.project')
					->uri('cbo.project.category.list')->sequence($this->sequence . '0801');
			});
			$id = "156652700a0b11e7bbda71d0eed97c22";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.project.project')->name('项目')->parent('cbo.project')
					->uri('cbo.project.list')->sequence($this->sequence . '0802');
			});

			//批号
			$id = "0a807ad00a0b11e78980c3030492b119";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.lot')->name('批号')->parent('cbo')
					->uri('cbo.lot')->sequence($this->sequence . '1000');
			});
			$id = "0a807d600a0b11e794f3d566d1a4e87a";
			Menu::build(function (Builder $builder) use ($id) {
				$builder->id($id)->code('cbo.lot.lot')->name('批号')->parent('cbo.lot')
					->uri('cbo.lot.list')->sequence($this->sequence . '1001');
			});
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Menu::where('code', 'like', 'cbo%')->delete();
	}
}
