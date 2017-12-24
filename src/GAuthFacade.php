<?php
namespace Gmf\Sys;
use Illuminate\Support\Facades\Facade;

/**
 * UuidFacade
 *
 */
class GAuthFacade extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'gauth';
	}
}