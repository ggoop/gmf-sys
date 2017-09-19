<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Seeder;

class GmfSysEntSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$id = '12343344';
		$b = new Builder;
		$b->name('test')->code('test');
		$ent = Models\Ent::updateOrCreate(['id' => $id], $b->toArray());
	}
}
