<?php

namespace Gmf\Sys\Traits;
use Gmf\Sys\Events\ModelObserver;

trait HasGuard {
	protected static function bootHasGuard() {
		static::observe(ModelObserver::class);
	}
}
