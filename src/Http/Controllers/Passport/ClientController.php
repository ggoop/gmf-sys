<?php

namespace Gmf\Sys\Http\Controllers\Passport;

use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Passport\ClientRepository;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientController extends Controller {
	/**
	 * The client repository instance.
	 *
	 * @var \Gmf\Passport\ClientRepository
	 */
	protected $clients;

	/**
	 * The validation factory implementation.
	 *
	 * @var \Illuminate\Contracts\Validation\Factory
	 */
	protected $validation;

	/**
	 * Create a client controller instance.
	 *
	 * @param  \Gmf\Passport\ClientRepository  $clients
	 * @param  \Illuminate\Contracts\Validation\Factory  $validation
	 * @return void
	 */
	public function __construct(ClientRepository $clients,
		ValidationFactory $validation) {
		$this->clients = $clients;
		$this->validation = $validation;
	}

	/**
	 * Get all of the clients for the authenticated user.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function forUser(Request $request) {
		$userId = $request->user()->getKey();

		return $this->clients->activeForUser($userId)->makeVisible('secret');
	}

	/**
	 * Store a new client.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$validator = $this->validation->make($request->all(), [
			'name' => 'required|max:255',
			'redirect' => 'required|url',
		]);

		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}

		$data = $this->clients->create(
			$request->user()->getKey(), $request->name, $request->redirect
		)->makeVisible('secret');

		return $this->toJson($data);
	}

	/**
	 * Update the given client.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $clientId
	 * @return \Illuminate\Http\Response|\Gmf\Passport\Client
	 */
	public function update(Request $request, $clientId) {
		$client = $this->clients->findForUser($clientId, $request->user()->getKey());

		if (!$client) {
			return new Response('', 404);
		}

		$validator = $this->validation->make($request->all(), [
			'name' => 'required|max:255',
			'redirect' => 'required|url',
		]);
		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}

		$data = $this->clients->update(
			$client, $request->name, $request->redirect
		);

		return $this->toJson($data);
	}

	/**
	 * Delete the given client.
	 *
	 * @param  Request  $request
	 * @param  string  $clientId
	 * @return Response
	 */
	public function destroy(Request $request, $clientId) {
		$client = $this->clients->findForUser($clientId, $request->user()->getKey());

		if (!$client) {
			return new Response('', 404);
		}

		$this->clients->delete(
			$client
		);

		return $this->toJson(true);
	}
}
