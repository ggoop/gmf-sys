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
		$id = config('gmf.ent.id');
		$name = config('gmf.ent.name');
		$account = config('gmf.ent.user');
		if (!$id || !$account) {
			return;
		}
		$user = config('gmf.user.model')::findByAccount($account, 'sys');
		if (empty($user)) {
			throw new \Exception("$account is not exsts!");
		}
		$b = new Builder;
		$b->name($name)->code($code);
		$ent = Models\Ent::updateOrCreate(['id' => $id], $b->toArray());
		if ($ent) {
			Models\Ent::addUser($ent->id, $user->id);
		}
	}
}
