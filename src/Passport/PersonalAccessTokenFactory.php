<?php

namespace Gmf\Sys\Passport;

use Lcobucci\JWT\Parser as JwtParser;
use League\OAuth2\Server\AuthorizationServer;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class PersonalAccessTokenFactory {
	/**
	 * The authorization server instance.
	 *
	 * @var \League\OAuth2\Server\AuthorizationServer
	 */
	protected $server;

	/**
	 * The client repository instance.
	 *
	 * @var \Gmf\Passport\ClientRepository
	 */
	protected $clients;

	/**
	 * The token repository instance.
	 *
	 * @var \Gmf\Passport\TokenRepository
	 */
	protected $tokens;

	/**
	 * The JWT token parser instance.
	 *
	 * @var \Lcobucci\JWT\Parser
	 */
	protected $jwt;

	/**
	 * Create a new personal access token factory instance.
	 *
	 * @param  \League\OAuth2\Server\AuthorizationServer  $server
	 * @param  \Gmf\Passport\ClientRepository  $clients
	 * @param  \Gmf\Passport\TokenRepository  $tokens
	 * @param  \Lcobucci\JWT\Parser  $jwt
	 * @return void
	 */
	public function __construct(AuthorizationServer $server,
		ClientRepository $clients,
		TokenRepository $tokens,
		JwtParser $jwt) {
		$this->jwt = $jwt;
		$this->tokens = $tokens;
		$this->server = $server;
		$this->clients = $clients;
	}

	/**
	 * Create a new personal access token.
	 *
	 * @param  mixed  $userId
	 * @param  string  $name
	 * @param  array  $scopes
	 * @return \Gmf\Passport\PersonalAccessTokenResult
	 */
	public function make($userId, $name, array $scopes = [], $clientId = false) {
		$response = $this->dispatchRequestToAuthorizationServer(
			$this->createRequest($this->clients->personalAccessClient($clientId), $userId, $scopes)
		);

		$token = tap($this->findAccessToken($response), function ($token) use ($userId, $name) {
			$this->tokens->save($token->forceFill([
				'user_id' => $userId,
				'name' => $name,
			]));
		});

		return new PersonalAccessTokenResult(
			$response['access_token'], $token
		);
	}

	/**
	 * Create a request instance for the given client.
	 *
	 * @param  \Gmf\Passport\Client  $client
	 * @param  mixed  $userId
	 * @param  array  $scopes
	 * @return \Zend\Diactoros\ServerRequest
	 */
	protected function createRequest($client, $userId, array $scopes) {
		return (new ServerRequest)->withParsedBody([
			'grant_type' => 'personal_access',
			'client_id' => $client->id,
			'client_secret' => $client->secret,
			'user_id' => $userId,
			'scope' => implode(' ', $scopes),
		]);
	}

	/**
	 * Dispatch the given request to the authorization server.
	 *
	 * @param  \Zend\Diactoros\ServerRequest  $request
	 * @return array
	 */
	protected function dispatchRequestToAuthorizationServer(ServerRequest $request) {
		return json_decode($this->server->respondToAccessTokenRequest(
			$request, new Response
		)->getBody()->__toString(), true);
	}

	/**
	 * Get the access token instance for the parsed response.
	 *
	 * @param  array  $response
	 * @return Token
	 */
	protected function findAccessToken(array $response) {
		return $this->tokens->find(
			$this->jwt->parse($response['access_token'])->getClaim('jti')
		);
	}
}
