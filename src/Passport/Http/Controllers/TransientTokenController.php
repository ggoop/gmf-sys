<?php

namespace Gmf\Sys\Passport\Http\Controllers;

use Gmf\Sys\Passport\ApiTokenCookieFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransientTokenController {
	/**
	 * The cookie factory instance.
	 *
	 * @var ApiTokenCookieFactory
	 */
	protected $cookieFactory;

	/**
	 * Create a new controller instance.
	 *
	 * @param  ApiTokenCookieFactory  $cookieFactory
	 * @return void
	 */
	public function __construct(ApiTokenCookieFactory $cookieFactory) {
		$this->cookieFactory = $cookieFactory;
	}

	/**
	 * Get a fresh transient token cookie for the authenticated user.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function refresh(Request $request) {
		return (new Response('Refreshed.'))->withCookie($this->cookieFactory->make(
			$request->user()->getKey(), $request->session()->token()
		));
	}
}