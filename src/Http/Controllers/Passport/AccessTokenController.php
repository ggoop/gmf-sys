<?php

namespace Gmf\Sys\Http\Controllers\Passport;

use Gmf\Sys\Passport\TokenRepository;
use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;

class AccessTokenController {
	use HandlesOAuthErrors;

	/**
	 * The authorization server.
	 *
	 * @var \League\OAuth2\Server\AuthorizationServer
	 */
	protected $server;

	/**
	 * The token repository instance.
	 *
	 * @var \Gmf\Passport\TokenRepository
	 */
	protected $tokens;

	/**
	 * The JWT parser instance.
	 *
	 * @var \Lcobucci\JWT\Parser
	 */
	protected $jwt;

	/**
	 * Create a new controller instance.
	 *
	 * @param  \League\OAuth2\Server\AuthorizationServer  $server
	 * @param  \Gmf\Passport\TokenRepository  $tokens
	 * @param  \Lcobucci\JWT\Parser  $jwt
	 * @return void
	 */
	public function __construct(AuthorizationServer $server,
		TokenRepository $tokens,
		JwtParser $jwt) {
		$this->jwt = $jwt;
		$this->server = $server;
		$this->tokens = $tokens;
	}

	/**
	 * Authorize a client to access the user's account.
	 *
	 * @param  \Psr\Http\Message\ServerRequestInterface  $request
	 * @return \Illuminate\Http\Response
	 */
	public function issueToken(ServerRequestInterface $request) {
		//return $request->getParsedBody();
		return $this->withErrorHandling(function () use ($request) {
			return $this->server->respondToAccessTokenRequest($request, new Psr7Response);
		});
	}
}
