<?php
namespace Gmf\Sys\Facades;
use Illuminate\Support\Facades\Facade;

/**
 * UuidFacade
 *
 */
class GAuth extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'gauth';
	}
}