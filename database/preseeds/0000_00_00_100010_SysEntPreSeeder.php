<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models;
use Illuminate\Database\Seeder;

class SysEntPreSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$id = config('app.key');
		$name = config('app.name');
		$dchost = config('APP.url');
		if (!$id) {
			return;
		}
		$b = new Builder;
		$b->name($name)->code($id)->dc_host($dchost);
		$ent = Models\Ent::updateOrCreate(['id' => $id], $b->toArray());

		$uid = config('gmf.admin.id');
		if ($uid && $ent) {
			Models\Ent::addUser($ent->id, $uid);
		}

		$id = config('gmf.ent.id');
		$name = config('gmf.ent.name');
		$dchost = config('gmf.ent.dc_host');
		if (!$id) {
			return;
		}
		$b = new Builder;
		$b->name($name)->code($id)->dc_host($dchost);
		$ent = Models\Ent::updateOrCreate(['id' => $id], $b->toArray());
		if ($uid && $ent) {
			Models\Ent::addUser($ent->id, $uid);
		}
	}
}
