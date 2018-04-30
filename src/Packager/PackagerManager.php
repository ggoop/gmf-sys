<?php

namespace Gmf\Sys\Packager;
use Illuminate\Http\Request;

class PackagerManager {

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
	protected $guards = [];
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
		$this->guards[] = new Packager;
	}
	public function guard() {
		return $this->guards[0];
	}
	/**
	 * Dynamically call the default driver instance.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters) {
		return $this->guard()->{$method}(...$parameters);
	}
}
