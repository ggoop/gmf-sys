<?php

namespace Gmf\Sys\Traits;
use Gmf\Sys\Models\DbHis;

/*
可以快照的
 */
trait Snapshotable {
	protected static function bootSnapshotable() {
		static::deleted(function ($model) {
			$model->snapshot();
		});
	}
	public function snapshot() {
		$d = new DbHis;
		$d->row_id = $this->id;
		$d->tableName = $this->table;
		$d->row_type = static::class;
		$d->data = $this->toJson();
		$d->save();

	}
}
