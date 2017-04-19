<?php

namespace Gmf\Sys\Passport\Http\Controllers;

use Gmf\Sys\Passport\Passport;

class ScopeController {
	/**
	 * Get all of the available scopes for the application.
	 *
	 * @return \Illuminate\Support\Collection
	 */
	public function all() {
		return Passport::scopes();
	}
}