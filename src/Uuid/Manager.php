<?php

namespace Gmf\Sys\Uuid;
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
	}
	public function generate() {
		return Uuid::generate(1, 'gmf', Uuid::NS_DNS, "");
	}
	public function sequence($format = 'ymdxxxxxx', $tag = '', $count = 1) {
		return Uuid::sequence($format, $tag, $count);
	}
}
