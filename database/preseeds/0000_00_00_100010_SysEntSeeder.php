<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Seeder;

class SysEntSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		$id = config('app.key');
		$name = config('app.name');
		if (!$id) {
			return;
		}
		$b = new Builder;
		$b->name($name)->code($id);
		$ent = Models\Ent::updateOrCreate(['id' => $id], $b->toArray());

		$id = config('gmf.ent.id');
		$name = config('gmf.ent.name');
		if (!$id) {
			return;
		}
		$b = new Builder;
		$b->name($name)->code($id);
		$ent = Models\Ent::updateOrCreate(['id' => $id], $b->toArray());
	}
}
