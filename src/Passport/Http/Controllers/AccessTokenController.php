<?php

namespace Gmf\Sys\Passport\Http\Controllers;

use Gmf\Sys\Passport\Passport;
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
	 * @var AuthorizationServer
	 */
	protected $server;

	/**
	 * The token repository instance.
	 *
	 * @var TokenRepository
	 */
	protected $tokens;

	/**
	 * The JWT parser instance.
	 *
	 * @var JwtParser
	 */
	protected $jwt;

	/**
	 * Create a new controller instance.
	 *
	 * @param  AuthorizationServer  $server
	 * @param  TokenRepository  $tokens
	 * @param  JwtParser  $jwt
	 * @return void
	 */
	public function __construct(AuthorizationServer $server, TokenRepository $tokens, JwtParser $jwt) {
		$this->jwt = $jwt;
		$this->server = $server;
		$this->tokens = $tokens;
	}

	/**
	 * Authorize a client to access the user's account.
	 *
	 * @param  ServerRequestInterface  $request
	 * @return Response
	 */
	public function issueToken(ServerRequestInterface $request) {
		return $this->withErrorHandling(function () use ($request) {
			return $this->server->respondToAccessTokenRequest($request, new Psr7Response);
		});
	}
}
