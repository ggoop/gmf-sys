<?php
namespace Gmf\Sys\Facades;
use Illuminate\Support\Facades\Facade;

/**
 * Packager
 *
 */
class Packager extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'packager';
	}
}