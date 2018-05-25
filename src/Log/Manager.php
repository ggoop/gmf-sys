<?php

namespace Gmf\Sys\Log;
use Illuminate\Http\Request;

class Manager {

	protected $callback;

	/**
	 * The request instance.
	 *
	 * @var \Illuminate\Http\Request
	 */
	protected $request;

	/**
	 * The application instance.
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app;
	protected $guard = false;
	/**
	 * Create a new Auth manager instance.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function __construct($app, callable $callback = null) {
		$this->app = $app;
		$this->callback = $callback;
		$this->request = $app['request'];
	}
}
