<?php

namespace Gmf\Sys\GAuth;

use Gmf\Sys\Models;
use Illuminate\Http\Request;
use Auth;
class GAuthManager
{

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
	public function __construct($app, callable $callback = null)
	{
		$this->app = $app;
		$this->callback = $callback;
		$this->request = $app['request'];
		$this->guards[] = new GAuthGuard;
		$this->resolveEntUsing();
	}
	public function guard()
	{
		return $this->guards[0];
	}
	private function resolveEntUsing()
	{
		$user= Auth::user();
		if($user){
			$this->guard()->setUser($user);
		}
		if ($this->request->hasHeader('Ent') && $entId = $this->request->header('Ent')) {
			$this->guard()->setEnt(Models\Ent\Ent::where('id', $entId)->orWhere('openid', $entId)->first());
		}
	}
	/**
	 * Dynamically call the default driver instance.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		return $this->guard()->{$method}(...$parameters);
	}
}
