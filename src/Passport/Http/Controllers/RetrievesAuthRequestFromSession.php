<?php

namespace Gmf\Sys\Passport\Http\Controllers;

use Exception;
use Gmf\Sys\Passport\Bridge\User;
use Illuminate\Http\Request;

trait RetrievesAuthRequestFromSession {
	/**
	 * Get the authorization request from the session.
	 *
	 * @param  Request  $request
	 * @return AuthorizationRequest
	 */
	protected function getAuthRequestFromSession(Request $request) {
		return tap($request->session()->get('authRequest'), function ($authRequest) use ($request) {
			if (!$authRequest) {
				throw new Exception('Authorization request was not present in the session.');
			}

			$authRequest->setUser(new User($request->user()->getKey()));

			$authRequest->setAuthorizationApproved(true);
		});
	}
}
