<?php
namespace Gmf\Sys\Facades;
use Illuminate\Support\Facades\Facade;

/**
 * UuidFacade
 *
 */
class GLog extends Facade {
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'glog';
	}
}